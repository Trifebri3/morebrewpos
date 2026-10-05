<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Kedai;
use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Voucher;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\Absensi;
use App\Models\SesiKasir;

class SyncController extends Controller
{
    /**
     * PULL DATA: Mengirimkan seluruh data master & status kedai dari server web ke aplikasi mobile POS.
     * Mengembalikan profil kedai, pengguna/kasir, kategori, menu produk, meja, voucher, dsb.
     */
    public function pull(Request $request)
    {
        $kedai = Kedai::first();
        if (!$kedai) {
            $kedai = Kedai::create([
                'name' => 'Kedai MORE BREW',
                'address' => 'Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung, Jawa Barat 40261',
                'phone' => '0812-3456-7890',
                'is_active' => true,
                'wifi_ssid' => 'moreandmore',
                'wifi_password' => 'bolehmintasenyumnya?',
                'instagram' => 'morebrewcoffee',
            ]);
        }

        // Data Users (Admin & Kasir)
        $users = User::select('id', 'name', 'email', 'role', 'phone', 'kedai_id')->get()->map(function ($u) {
            return [
                'id' => (string) $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role ?? 'kasir',
                'pin' => '1234', // Default PIN untuk login offline cepat
                'phone' => $u->phone ?? '',
            ];
        });

        // Data Kategori
        $categories = Kategori::all()->map(function ($c) {
            return [
                'id' => (string) $c->id,
                'name' => $c->name,
                'icon' => $c->icon ?? 'category',
            ];
        });

        // Data Produk
        $products = Produk::where('is_active', true)->get()->map(function ($p) {
            return [
                'id' => (string) $p->id,
                'name' => $p->name,
                'category' => $p->category ?? 'Minuman',
                'price' => (double) $p->price,
                'stock' => (int) $p->stock,
                'sku' => $p->sku ?? ('MB-' . $p->id),
                'image' => $p->image ?? '',
                'isActive' => (bool) $p->is_active,
            ];
        });

        // Data Meja
        $tables = Meja::all()->map(function ($m, $index) {
            return [
                'id' => (string) $m->id,
                'number' => $m->name,
                'capacity' => 4,
                'status' => 'tersedia',
            ];
        });

        // Data Voucher
        $vouchers = Voucher::where('status', true)->get()->map(function ($v) {
            return [
                'id' => (string) $v->id,
                'code' => $v->kode,
                'type' => $v->tipe_diskon === 'persen' ? 'persen' : 'nominal',
                'value' => (double) $v->nilai_diskon,
                'minOrder' => (double) $v->minimal_belanja,
                'maxDiscount' => (double) $v->nilai_diskon,
                'quota' => (int) ($v->kuota ?? 100),
                'used' => (int) ($v->terpakai ?? 0),
                'isActive' => (bool) $v->status,
            ];
        });

        return response()->json([
            'status' => 'success',
            'server_time' => now()->toIso8601String(),
            'data' => [
                'kedai' => [
                    'id' => (string) $kedai->id,
                    'name' => $kedai->name,
                    'address' => $kedai->address ?? 'Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung, Jawa Barat 40261',
                    'phone' => $kedai->phone ?? '0812-3456-7890',
                    'taxPercentage' => 11.0,
                    'receiptHeader' => 'something, between home and everywhere',
                    'receiptFooter' => 'Silakan datang kembali!',
                    'wifiSsid' => $kedai->wifi_ssid ?? 'moreandmore',
                    'wifiPassword' => $kedai->wifi_password ?? 'bolehmintasenyumnya?',
                    'instagram' => $kedai->instagram ?? 'morebrewcoffee',
                ],
                'users' => $users,
                'categories' => $categories,
                'products' => $products,
                'tables' => $tables,
                'vouchers' => $vouchers,
            ],
        ]);
    }

    /**
     * PUSH DATA: Menerima kumpulan data transaksi, pengeluaran, absensi, & sesi kasir
     * yang dibuat dari aplikasi mobile saat offline/online untuk disimpan ke database web.
     */
    public function push(Request $request)
    {
        $kedai = Kedai::first();
        $kedaiId = $kedai ? $kedai->id : null;

        $syncedTransactions = 0;
        $syncedExpenses = 0;
        $syncedAttendances = 0;
        $syncedSessions = 0;

        // 1. Simpan Transaksi Penjualan
        $transactions = $request->input('transactions', []);
        if (is_array($transactions)) {
            foreach ($transactions as $tx) {
                $inv = $tx['invoiceNumber'] ?? $tx['invoice_number'] ?? null;
                if (!$inv) continue;

                $existing = Transaksi::where('invoice_number', $inv)->first();
                if (!$existing) {
                    $voucherId = null;
                    if (!empty($tx['voucherCode'])) {
                        $v = Voucher::where('kode', $tx['voucherCode'])->first();
                        if ($v) {
                            $voucherId = $v->id;
                            $v->increment('terpakai');
                            if ($v->kuota !== null && $v->kuota > 0) {
                                $v->decrement('kuota');
                            }
                        }
                    }

                    $rawItems = $tx['items'] ?? [];
                    if (is_string($rawItems)) {
                        $rawItems = json_decode($rawItems, true) ?? [];
                    }

                    Transaksi::create([
                        'invoice_number' => $inv,
                        'customer_name'  => $tx['customerName'] ?? $tx['customer_name'] ?? 'Pelanggan Walk-In',
                        'order_type'     => $tx['orderType'] ?? $tx['order_type'] ?? 'dine_in',
                        'items'          => $rawItems,
                        'subtotal'       => $tx['subtotal'] ?? 0,
                        'discount_amount'=> $tx['discount'] ?? $tx['discount_amount'] ?? 0,
                        'voucher_id'     => $voucherId,
                        'tax'            => $tx['tax'] ?? 0,
                        'total'          => $tx['total'] ?? 0,
                        'payment_method' => $tx['paymentMethod'] ?? $tx['payment_method'] ?? 'cash',
                        'amount_paid'    => $tx['amountPaid'] ?? $tx['amount_paid'] ?? ($tx['total'] ?? 0),
                        'created_at'     => isset($tx['createdAt']) ? Carbon::parse($tx['createdAt']) : now(),
                    ]);

                    // Kurangi stok produk di server
                    foreach ($rawItems as $item) {
                        $pId = $item['productId'] ?? $item['id'] ?? null;
                        $qty = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
                        if ($pId) {
                            $prod = Produk::find($pId);
                            if (!$prod && isset($item['sku'])) {
                                $prod = Produk::where('sku', $item['sku'])->first();
                            }
                            if (!$prod && isset($item['name'])) {
                                $prod = Produk::where('name', $item['name'])->first();
                            }
                            if ($prod) {
                                $prod->decrement('stock', $qty);
                            }
                        }
                    }

                    $syncedTransactions++;
                }
            }
        }

        // 2. Simpan Pengeluaran / Belanja Harian
        $expenses = $request->input('expenses', []);
        if (is_array($expenses)) {
            foreach ($expenses as $exp) {
                $name = $exp['name'] ?? 'Pengeluaran Kasir';
                $amount = $exp['amount'] ?? 0;
                $date = isset($exp['createdAt']) ? Carbon::parse($exp['createdAt'])->toDateString() : today()->toDateString();

                // Cek duplikasi pengeluaran pada tanggal & nominal yang sama
                $exists = Pengeluaran::where('nama_item', $name)
                    ->where('nominal', $amount)
                    ->whereDate('tanggal', $date)
                    ->exists();

                if (!$exists) {
                    $userId = null;
                    if (!empty($exp['cashierId'])) {
                        $user = User::where('id', $exp['cashierId'])->orWhere('email', $exp['cashierId'])->first();
                        $userId = $user?->id;
                    }
                    if (!$userId) {
                        $user = User::where('role', 'kasir')->first();
                        $userId = $user?->id;
                    }

                    Pengeluaran::create([
                        'kedai_id'   => $kedaiId,
                        'user_id'    => $userId,
                        'tanggal'    => $date,
                        'nama_item'  => $name,
                        'nominal'    => $amount,
                        'keterangan' => ($exp['category'] ?? '') . ': ' . ($exp['notes'] ?? ''),
                        'status'     => $exp['status'] ?? 'pending',
                    ]);
                    $syncedExpenses++;
                }
            }
        }

        // 3. Simpan Absensi Karyawan
        $attendances = $request->input('attendances', []);
        if (is_array($attendances)) {
            foreach ($attendances as $att) {
                $userId = null;
                if (!empty($att['userId'])) {
                    $user = User::where('id', $att['userId'])->orWhere('email', $att['userId'])->first();
                    $userId = $user?->id;
                }
                if (!$userId) {
                    $user = User::where('role', 'kasir')->first();
                    $userId = $user?->id;
                }

                if ($userId) {
                    $dateStr = $att['date'] ?? today()->toDateString();
                    // Clock In
                    if (!empty($att['clockIn'])) {
                        $existsIn = Absensi::where('user_id', $userId)
                            ->where('type', 'Masuk')
                            ->whereDate('created_at', $dateStr)
                            ->exists();
                        if (!$existsIn) {
                            Absensi::create([
                                'user_id' => $userId,
                                'type' => 'Masuk',
                                'status' => 'valid',
                                'created_at' => Carbon::parse($dateStr . ' ' . $att['clockIn']),
                            ]);
                            $syncedAttendances++;
                        }
                    }
                    // Clock Out
                    if (!empty($att['clockOut'])) {
                        $existsOut = Absensi::where('user_id', $userId)
                            ->where('type', 'Keluar')
                            ->whereDate('created_at', $dateStr)
                            ->exists();
                        if (!$existsOut) {
                            Absensi::create([
                                'user_id' => $userId,
                                'type' => 'Keluar',
                                'status' => 'valid',
                                'created_at' => Carbon::parse($dateStr . ' ' . $att['clockOut']),
                            ]);
                            $syncedAttendances++;
                        }
                    }
                }
            }
        }

        // 4. Simpan Sesi Kasir (Buka / Tutup)
        $sessions = $request->input('sessions', []);
        if (is_array($sessions)) {
            foreach ($sessions as $ses) {
                $userId = null;
                if (!empty($ses['userId'])) {
                    $u = User::where('id', $ses['userId'])->orWhere('email', $ses['userId'])->first();
                    $userId = $u?->id;
                }
                if (!$userId) {
                    $u = User::where('role', 'kasir')->first();
                    $userId = $u?->id;
                }

                if ($userId) {
                    $openTime = isset($ses['openedAt']) ? Carbon::parse($ses['openedAt']) : now();
                    $closeTime = isset($ses['closedAt']) ? Carbon::parse($ses['closedAt']) : null;

                    $sesRecord = SesiKasir::where('user_id', $userId)
                        ->whereDate('waktu_buka', $openTime->toDateString())
                        ->first();

                    if (!$sesRecord) {
                        SesiKasir::create([
                            'user_id' => $userId,
                            'waktu_buka' => $openTime,
                            'waktu_tutup' => $closeTime,
                            'modal_awal' => $ses['initialCash'] ?? 0,
                            'total_pendapatan' => ($ses['totalCashSales'] ?? 0) + ($ses['totalNonCashSales'] ?? 0),
                            'uang_fisik' => $ses['physicalCash'] ?? 0,
                            'selisih' => $ses['difference'] ?? 0,
                            'status' => $ses['status'] ?? 'open',
                        ]);
                        $syncedSessions++;
                    } else if ($closeTime) {
                        $sesRecord->update([
                            'waktu_tutup' => $closeTime,
                            'total_pendapatan' => ($ses['totalCashSales'] ?? 0) + ($ses['totalNonCashSales'] ?? 0),
                            'uang_fisik' => $ses['physicalCash'] ?? 0,
                            'selisih' => $ses['difference'] ?? 0,
                            'status' => 'closed',
                        ]);
                        $syncedSessions++;
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Sinkronisasi berhasil diproses oleh server web MoreBrew POS',
            'synced' => [
                'transactions' => $syncedTransactions,
                'expenses' => $syncedExpenses,
                'attendances' => $syncedAttendances,
                'sessions' => $syncedSessions,
            ],
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * LOGIN API: Verifikasi kredensial kasir/admin dari aplikasi mobile
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun dengan email ini tidak ditemukan.',
            ], 404);
        }

        // Cek password atau pin default
        $valid = Hash::check($request->password, $user->password)
            || $request->password === 'password'
            || $request->password === '1234';

        if (!$valid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kata sandi atau PIN tidak sesuai.',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'user' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'kasir',
                'phone' => $user->phone ?? '',
                'kedai_id' => $user->kedai_id,
            ],
        ]);
    }
}
