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
                'name' => 'MOREBREWW',
                'address' => 'Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung, Jawa Barat 40261',
                'phone' => '',
                'is_active' => true,
                'wifi_ssid' => 'moreandmore',
                'wifi_password' => 'bolehlihatsenyumnya?',
                'instagram' => 'morebrewcoffee',
            ]);
        } else {
            // Bersihkan tulisan 'Kedai' dan sesuaikan nama kedai jika masih 'Kedai MORE BREW'
            $cleanedName = trim(preg_replace('/^kedai\s+/i', '', $kedai->name));
            if (empty($cleanedName) || strtoupper($cleanedName) === 'MORE BREW' || stripos($kedai->name, 'kedai') !== false) {
                $kedai->name = 'MOREBREWW';
                $kedai->save();
            }
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

        // Data Voucher (Auto-seed jika belum ada di database)
        if (Voucher::count() === 0) {
            $kedaiId = $kedai ? $kedai->id : 1;
            $defaultVouchers = [
                ['kedai_id' => $kedaiId, 'kode' => 'MOREBREW10', 'nama' => 'Diskon 10% Spesial', 'tipe_diskon' => 'persen', 'nilai_diskon' => 10, 'minimal_belanja' => 25000, 'kuota' => 500, 'status' => true],
                ['kedai_id' => $kedaiId, 'kode' => 'HEMAT5K', 'nama' => 'Potongan Hemat Rp 5.000', 'tipe_diskon' => 'nominal', 'nilai_diskon' => 5000, 'minimal_belanja' => 30000, 'kuota' => 300, 'status' => true],
                ['kedai_id' => $kedaiId, 'kode' => 'DISKON15', 'nama' => 'Promo Diskon 15%', 'tipe_diskon' => 'persen', 'nilai_diskon' => 15, 'minimal_belanja' => 50000, 'kuota' => 200, 'status' => true],
                ['kedai_id' => $kedaiId, 'kode' => 'KOPISANTAI', 'nama' => 'Potongan Ngopi Rp 10.000', 'tipe_diskon' => 'nominal', 'nilai_diskon' => 10000, 'minimal_belanja' => 60000, 'kuota' => 150, 'status' => true],
            ];
            foreach ($defaultVouchers as $dv) {
                Voucher::create($dv);
            }
        }

        $vouchers = Voucher::where('status', true)->get()->map(function ($v) {
            $isPercent = $v->tipe_diskon === 'persen' || stripos($v->tipe_diskon, 'persen') !== false;
            return [
                'id' => (string) $v->id,
                'code' => $v->kode,
                'type' => $isPercent ? 'persen' : 'nominal',
                'value' => (double) $v->nilai_diskon,
                'minOrder' => (double) ($v->minimal_belanja ?? 0),
                'maxDiscount' => (double) ($v->maksimal_diskon ?? ($isPercent ? 25000 : 0)),
                'quota' => (int) ($v->kuota ?? 100),
                'used' => (int) ($v->terpakai ?? 0),
                'isActive' => (bool) $v->status,
            ];
        });

        // Data Transaksi Terbaru untuk sinkronisasi riwayat
        $recentTransactions = Transaksi::latest()->take(50)->get()->map(function ($t) {
            return [
                'id' => (string) $t->id,
                'invoiceNumber' => $t->invoice_number,
                'customerName' => $t->customer_name ?? 'Pelanggan Walk-In',
                'orderType' => $t->order_type ?? 'dine_in',
                'items' => $t->items ?? [],
                'subtotal' => (double) $t->subtotal,
                'discount' => (double) $t->discount_amount,
                'voucherCode' => $t->voucher ? $t->voucher->kode : null,
                'tax' => (double) $t->tax,
                'total' => (double) $t->total,
                'paymentMethod' => $t->payment_method ?? 'cash',
                'amountPaid' => (double) $t->amount_paid,
                'change' => max(0, (double) $t->amount_paid - (double) $t->total),
                'cashierId' => '1',
                'cashierName' => 'Kasir',
                'status' => $t->is_refunded ? 'refunded' : ($t->payment_method === 'pending' || $t->payment_method === 'bayar_nanti' ? 'pending' : 'selesai'),
                'refundReason' => $t->refund_reason,
                'createdAt' => $t->created_at ? $t->created_at->toIso8601String() : now()->toIso8601String(),
                'syncStatus' => 'synced',
            ];
        });

        // Data Pesanan yang Menunggu Pembayaran (QR Meja 'pending' & Kasir 'bayar_nanti')
        $pendingTableOrders = Transaksi::whereIn('payment_method', ['pending', 'bayar_nanti'])
            ->latest()
            ->get()
            ->map(function ($t) {
                return [
                    'id' => (string) $t->id,
                    'invoiceNumber' => $t->invoice_number,
                    'customerName' => $t->customer_name ?? 'Pelanggan',
                    'orderType' => $t->order_type ?? 'dine_in',
                    'items' => $t->items ?? [],
                    'subtotal' => (double) $t->subtotal,
                    'discount' => (double) $t->discount_amount,
                    'tax' => (double) $t->tax,
                    'total' => (double) $t->total,
                    'paymentMethod' => $t->payment_method ?? 'pending',
                    'amountPaid' => (double) ($t->amount_paid ?? 0),
                    'change' => 0.0,
                    'cashierId' => '',
                    'cashierName' => '',
                    'status' => 'pending',
                    'createdAt' => $t->created_at ? $t->created_at->toIso8601String() : now()->toIso8601String(),
                    'syncStatus' => 'synced',
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
                    'phone' => $kedai->phone ?? '',
                    'taxPercentage' => (double) ($kedai->tax_percentage ?? 11.0),
                    'isTaxEnabled' => (bool) ($kedai->is_tax_enabled ?? true),
                    'taxName' => $kedai->tax_name ?? 'PB1 (Pajak Restoran)',
                    'dailyBudget' => (double) ($kedai->budget_harian ?? 1000000.0),
                    'receiptHeader' => $kedai->receipt_header ?? 'something, between home and everywhere',
                    'receiptFooter' => $kedai->receipt_footer ?? 'Silakan datang kembali!',
                    'wifiSsid' => $kedai->wifi_ssid ?? 'moreandmore',
                    'wifiPassword' => $kedai->wifi_password ?? 'bolehlihatsenyumnya?',
                    'instagram' => $kedai->instagram ?? '@morebrewcoffee',
                ],
                'users' => $users,
                'categories' => $categories,
                'products' => $products,
                'tables' => $tables,
                'vouchers' => $vouchers,
                'transactions' => $recentTransactions,
                'pending_orders' => $pendingTableOrders,
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

        // 0. Update Pengaturan Kedai & Pajak jika dikirim dari mobile POS
        if ($request->has('kedai') && is_array($request->input('kedai'))) {
            $kData = $request->input('kedai');
            if ($kedai) {
                if (isset($kData['name']) && !empty($kData['name'])) $kedai->name = $kData['name'];
                if (isset($kData['address'])) $kedai->address = $kData['address'];
                if (isset($kData['phone'])) $kedai->phone = $kData['phone'];
                if (isset($kData['taxPercentage'])) $kedai->tax_percentage = (float)$kData['taxPercentage'];
                if (isset($kData['isTaxEnabled'])) $kedai->is_tax_enabled = (bool)$kData['isTaxEnabled'];
                if (isset($kData['taxName'])) $kedai->tax_name = $kData['taxName'];
                if (isset($kData['dailyBudget'])) $kedai->budget_harian = (float)$kData['dailyBudget'];
                if (isset($kData['receiptHeader'])) $kedai->receipt_header = $kData['receiptHeader'];
                if (isset($kData['receiptFooter'])) $kedai->receipt_footer = $kData['receiptFooter'];
                if (isset($kData['wifiSsid'])) $kedai->wifi_ssid = $kData['wifiSsid'];
                if (isset($kData['wifiPassword'])) $kedai->wifi_password = $kData['wifiPassword'];
                if (isset($kData['instagram'])) $kedai->instagram = $kData['instagram'];
                $kedai->save();
            }
        }

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

                $cleanInv = preg_replace('/^INV-?/i', '', (string)$inv);
                $existing = Transaksi::where('invoice_number', $cleanInv)->orWhere('invoice_number', $inv)->first();
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

                    $custName = $tx['customerName'] ?? $tx['customer_name'] ?? 'Pelanggan Walk-In';
                    $custPhone = !empty($tx['customerPhone']) ? trim($tx['customerPhone']) : (!empty($tx['customer_phone']) ? trim($tx['customer_phone']) : '');
                    if (!empty($custPhone) && !str_contains($custName, $custPhone)) {
                        $custName .= ' (' . $custPhone . ')';
                    }

                    Transaksi::create([
                        'invoice_number' => $cleanInv,
                        'customer_name'  => $custName,
                        'order_type'     => $tx['orderType'] ?? $tx['order_type'] ?? 'dine_in',
                        'items'          => $rawItems,
                        'subtotal'       => $tx['subtotal'] ?? 0,
                        'discount_amount'=> $tx['discount'] ?? $tx['discount_amount'] ?? 0,
                        'voucher_id'     => $voucherId,
                        'tax'            => $tx['tax'] ?? 0,
                        'total'          => $tx['total'] ?? 0,
                        'payment_method' => $tx['paymentMethod'] ?? $tx['payment_method'] ?? 'cash',
                        'amount_paid'    => $tx['amountPaid'] ?? $tx['amount_paid'] ?? ($tx['total'] ?? 0),
                        'sesi_kasir_id'  => $tx['cashierSessionId'] ?? null,
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
                            'user_id'             => $userId,
                            'session_number'      => $ses['sessionNumber'] ?? null,
                            'previous_session_id' => $ses['previousSessionId'] ?? null,
                            'waktu_buka'          => $openTime,
                            'waktu_tutup'         => $closeTime,
                            'modal_awal'          => $ses['initialCash'] ?? 0,
                            'total_pendapatan'    => ($ses['totalCashSales'] ?? 0) + ($ses['totalNonCashSales'] ?? 0),
                            'total_cash_sales'    => $ses['totalCashSales'] ?? 0,
                            'total_non_cash_sales'=> $ses['totalNonCashSales'] ?? 0,
                            'cash_in'             => $ses['cashIn'] ?? 0,
                            'cash_out'            => $ses['cashOut'] ?? 0,
                            'cash_expense'        => $ses['cashExpense'] ?? 0,
                            'expected_balance'    => $ses['expectedCash'] ?? $ses['expected_balance'] ?? 0,
                            'uang_fisik'          => $ses['physicalCash'] ?? 0,
                            'selisih'             => $ses['difference'] ?? 0,
                            'status'              => $ses['status'] ?? 'open',
                            'catatan'             => $ses['closingNote'] ?? $ses['openingNote'] ?? null,
                        ]);
                        $syncedSessions++;
                    } else if ($closeTime) {
                        $sesRecord->update([
                            'session_number'      => $ses['sessionNumber'] ?? $sesRecord->session_number,
                            'previous_session_id' => $ses['previousSessionId'] ?? $sesRecord->previous_session_id,
                            'waktu_tutup'         => $closeTime,
                            'total_pendapatan'    => ($ses['totalCashSales'] ?? 0) + ($ses['totalNonCashSales'] ?? 0),
                            'total_cash_sales'    => $ses['totalCashSales'] ?? 0,
                            'total_non_cash_sales'=> $ses['totalNonCashSales'] ?? 0,
                            'cash_in'             => $ses['cashIn'] ?? 0,
                            'cash_out'            => $ses['cashOut'] ?? 0,
                            'cash_expense'        => $ses['cashExpense'] ?? 0,
                            'expected_balance'    => $ses['expectedCash'] ?? $ses['expected_balance'] ?? 0,
                            'uang_fisik'          => $ses['physicalCash'] ?? 0,
                            'selisih'             => $ses['difference'] ?? 0,
                            'status'              => 'closed',
                            'catatan'             => $ses['closingNote'] ?? $ses['openingNote'] ?? $sesRecord->catatan,
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

    /**
     * API: Ambil semua pesanan yang berstatus 'pending' (QR meja & Bayar Nanti)
     */
    public function getPendingTableOrders()
    {
        $orders = Transaksi::whereIn('payment_method', ['pending', 'bayar_nanti'])
            ->latest()
            ->get()
            ->map(function ($t) {
                return [
                    'id' => (string) $t->id,
                    'invoiceNumber' => $t->invoice_number,
                    'customerName' => $t->customer_name ?? 'Pelanggan',
                    'orderType' => $t->order_type ?? 'dine_in',
                    'items' => $t->items ?? [],
                    'subtotal' => (double) $t->subtotal,
                    'discount' => (double) $t->discount_amount,
                    'tax' => (double) $t->tax,
                    'total' => (double) $t->total,
                    'paymentMethod' => $t->payment_method ?? 'pending',
                    'amountPaid' => (double) ($t->amount_paid ?? 0),
                    'change' => 0.0,
                    'cashierId' => '',
                    'cashierName' => '',
                    'status' => 'pending',
                    'createdAt' => $t->created_at ? $t->created_at->toIso8601String() : now()->toIso8601String(),
                    'syncStatus' => 'synced',
                ];
            });

        return response()->json([
            'status' => 'success',
            'count' => $orders->count(),
            'data' => $orders,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * API: Kasir memproses pembayaran pesanan meja / bayar nanti
     * Mengubah status pesanan menjadi selesai/terbayar
     */
    public function payTableOrder(Request $request, $invoice)
    {
        $cleanInv = preg_replace('/^INV-?/i', '', (string)$invoice);
        $transaksi = Transaksi::where('invoice_number', $cleanInv)->orWhere('invoice_number', $invoice)->first();
        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan dengan invoice ' . $invoice . ' tidak ditemukan.',
            ], 404);
        }

        $paymentMethod = $request->input('payment_method', 'Tunai');
        $amountPaid = (double) $request->input('amount_paid', $transaksi->total);
        $cashierId = $request->input('cashier_id');
        $sesiKasirId = $request->input('sesi_kasir_id') ?? $request->input('cashier_session_id');

        $transaksi->update([
            'payment_method' => $paymentMethod,
            'amount_paid'    => $amountPaid,
            'user_id'        => $cashierId,
            'sesi_kasir_id'  => $sesiKasirId,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pesanan #' . $invoice . ' berhasil dibayar via ' . $paymentMethod . '.',
            'data'    => [
                'invoiceNumber' => $transaksi->invoice_number,
                'paymentMethod' => $transaksi->payment_method,
                'total'         => (double) $transaksi->total,
                'amountPaid'    => (double) $transaksi->amount_paid,
                'change'        => max(0, (double) $transaksi->amount_paid - (double) $transaksi->total),
                'status'        => 'selesai',
            ],
        ]);
    }

    /**
     * API: Ambil Pengaturan Kedai & Pajak dari Database Server
     */
    public function getSettings()
    {
        $kedai = Kedai::first();
        if (!$kedai) {
            $kedai = Kedai::create([
                'name' => 'More Brew Coffee',
                'address' => 'Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung, Jawa Barat 40261',
                'tax_percentage' => 11.0,
                'is_tax_enabled' => true,
                'tax_name' => 'PB1 (Pajak Restoran)',
                'budget_harian' => 1000000.0,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => (string) $kedai->id,
                'name' => $kedai->name,
                'address' => $kedai->address ?? '',
                'phone' => $kedai->phone ?? '',
                'taxPercentage' => (double) ($kedai->tax_percentage ?? 11.0),
                'isTaxEnabled' => (bool) ($kedai->is_tax_enabled ?? true),
                'taxName' => $kedai->tax_name ?? 'PB1 (Pajak Restoran)',
                'dailyBudget' => (double) ($kedai->budget_harian ?? 1000000.0),
                'receiptHeader' => $kedai->receipt_header ?? 'something, between home and everywhere',
                'receiptFooter' => $kedai->receipt_footer ?? 'Silakan datang kembali!',
                'wifiSsid' => $kedai->wifi_ssid ?? 'moreandmore',
                'wifiPassword' => $kedai->wifi_password ?? 'bolehlihatsenyumnya?',
                'instagram' => $kedai->instagram ?? '@morebrewcoffee',
            ],
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * API: Update Pengaturan Kedai & Pajak Langsung ke Database Server
     * Dipanggil dari Aplikasi POS Mobile saat admin mengubah konfigurasi kedai/pajak
     */
    public function updateSettings(Request $request)
    {
        $kedai = Kedai::first();
        if (!$kedai) {
            $kedai = new Kedai();
        }

        // Support camelCase or snake_case
        if ($request->has('name') && !empty($request->input('name'))) {
            $kedai->name = $request->input('name');
        }
        if ($request->has('address')) {
            $kedai->address = $request->input('address');
        }
        if ($request->has('phone')) {
            $kedai->phone = $request->input('phone');
        }

        if ($request->has('taxPercentage')) {
            $kedai->tax_percentage = (float) $request->input('taxPercentage');
        } elseif ($request->has('tax_percentage')) {
            $kedai->tax_percentage = (float) $request->input('tax_percentage');
        }

        if ($request->has('isTaxEnabled')) {
            $kedai->is_tax_enabled = filter_var($request->input('isTaxEnabled'), FILTER_VALIDATE_BOOLEAN);
        } elseif ($request->has('is_tax_enabled')) {
            $kedai->is_tax_enabled = filter_var($request->input('is_tax_enabled'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('taxName')) {
            $kedai->tax_name = $request->input('taxName');
        } elseif ($request->has('tax_name')) {
            $kedai->tax_name = $request->input('tax_name');
        }

        if ($request->has('dailyBudget')) {
            $kedai->budget_harian = (float) $request->input('dailyBudget');
        } elseif ($request->has('budget_harian')) {
            $kedai->budget_harian = (float) $request->input('budget_harian');
        }

        if ($request->has('receiptHeader')) {
            $kedai->receipt_header = $request->input('receiptHeader');
        } elseif ($request->has('receipt_header')) {
            $kedai->receipt_header = $request->input('receipt_header');
        }

        if ($request->has('receiptFooter')) {
            $kedai->receipt_footer = $request->input('receiptFooter');
        } elseif ($request->has('receipt_footer')) {
            $kedai->receipt_footer = $request->input('receipt_footer');
        }

        if ($request->has('wifiSsid')) {
            $kedai->wifi_ssid = $request->input('wifiSsid');
        } elseif ($request->has('wifi_ssid')) {
            $kedai->wifi_ssid = $request->input('wifi_ssid');
        }

        if ($request->has('wifiPassword')) {
            $kedai->wifi_password = $request->input('wifiPassword');
        } elseif ($request->has('wifi_password')) {
            $kedai->wifi_password = $request->input('wifi_password');
        }

        if ($request->has('instagram')) {
            $kedai->instagram = $request->input('instagram');
        }

        $kedai->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaturan kedai & pajak di server database berhasil diperbarui!',
            'data' => [
                'id' => (string) $kedai->id,
                'name' => $kedai->name,
                'address' => $kedai->address,
                'phone' => $kedai->phone,
                'taxPercentage' => (double) $kedai->tax_percentage,
                'isTaxEnabled' => (bool) $kedai->is_tax_enabled,
                'taxName' => $kedai->tax_name,
                'dailyBudget' => (double) $kedai->budget_harian,
                'receiptHeader' => $kedai->receipt_header,
                'receiptFooter' => $kedai->receipt_footer,
                'wifiSsid' => $kedai->wifi_ssid,
                'wifiPassword' => $kedai->wifi_password,
                'instagram' => $kedai->instagram,
            ],
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
