<?php

namespace App\Services\Admin;

use App\Models\Absensi;
use App\Models\Pengeluaran;
use App\Models\Produk;
use App\Models\SesiKasir;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function getDashboardData(): array
    {
        $dummyUser = User::where('role', 'admin')->first();
        $realtime = $this->getRealtimeMetrics();

        $navGroups = [
            'Main Menu' => [
                ['label' => 'Dashboard', 'url' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
                ['label' => 'Buka Aplikasi POS', 'url' => '/kasir/dashboard'],
            ],
            'Operasional' => [
                ['label' => 'Produk', 'url' => route('admin.operasional.produk.index'), 'active' => request()->routeIs('admin.operasional.produk.*')],
                ['label' => 'Kategori', 'url' => route('admin.operasional.kategori.index'), 'active' => request()->routeIs('admin.operasional.kategori.*')],
                ['label' => 'Stok Harian', 'url' => route('admin.operasional.stok'), 'active' => request()->routeIs('admin.operasional.stok')],
                ['label' => 'Belanja Harian', 'url' => route('admin.operasional.pengeluaran.index'), 'active' => request()->routeIs('admin.operasional.pengeluaran.*')],
            ],
            'Penjualan' => [
                ['label' => 'Transaksi', 'url' => route('admin.penjualan.transaksi'), 'active' => request()->routeIs('admin.penjualan.transaksi')],
                ['label' => 'Invoice', 'url' => route('admin.penjualan.invoice'), 'active' => request()->routeIs('admin.penjualan.invoice')],
                ['label' => 'Voucher Promo', 'url' => route('admin.penjualan.voucher.index'), 'active' => request()->routeIs('admin.penjualan.voucher.*')],
                ['label' => 'Refund', 'url' => route('admin.penjualan.refund'), 'active' => request()->routeIs('admin.penjualan.refund')],
                ['label' => 'Riwayat', 'url' => route('admin.penjualan.riwayat'), 'active' => request()->routeIs('admin.penjualan.riwayat')],
            ],
            'Kedai & Pajak' => [
                ['label' => 'Pengaturan Kedai & Pajak', 'url' => route('admin.kedai.pengaturan'), 'active' => request()->routeIs('admin.kedai.pengaturan*')],
                ['label' => 'Meja Restoran', 'url' => route('admin.kedai.meja.index'), 'active' => request()->routeIs('admin.kedai.meja.*')],
            ],
            'Staff' => [
                ['label' => 'Karyawan & Staff', 'url' => route('admin.staff.karyawan.index'), 'active' => request()->routeIs('admin.staff.karyawan.*')],
                ['label' => 'Shift', 'url' => route('admin.staff.shift'), 'active' => request()->routeIs('admin.staff.shift')],
                ['label' => 'Absensi', 'url' => route('admin.staff.absensi'), 'active' => request()->routeIs('admin.staff.absensi')],
                ['label' => 'Aktivitas', 'url' => route('admin.staff.aktivitas'), 'active' => request()->routeIs('admin.staff.aktivitas')],
            ],
            'Laporan' => [
                ['label' => 'Laporan Penjualan', 'url' => route('admin.laporan.penjualan'), 'active' => request()->routeIs('admin.laporan.penjualan')],
                ['label' => 'Laporan Produk', 'url' => route('admin.laporan.produk'), 'active' => request()->routeIs('admin.laporan.produk')],
                ['label' => 'Laporan Kas', 'url' => route('admin.laporan.kas'), 'active' => request()->routeIs('admin.laporan.kas')],
                ['label' => 'Laporan Pengeluaran', 'url' => route('admin.laporan.pengeluaran'), 'active' => request()->routeIs('admin.laporan.pengeluaran')],
                ['label' => 'Laporan Shift', 'url' => route('admin.laporan.shift'), 'active' => request()->routeIs('admin.laporan.shift')],
                ['label' => 'Laporan Staff', 'url' => route('admin.laporan.staff'), 'active' => request()->routeIs('admin.laporan.staff')],
            ],
        ];

        return array_merge([
            'role' => 'Admin Kedai',
            'dummyUser' => $dummyUser ? $dummyUser->name : 'Admin',
            'navGroups' => $navGroups,
        ], $realtime);
    }

    public function getRealtimeMetrics(): array
    {
        $today = Carbon::today();

        // 1. Omzet Hari Ini (Total penjualan non-refund)
        $omzetHariIni = (float) Transaksi::whereDate('created_at', $today)
            ->where('is_refunded', false)
            ->sum('total');

        // 2. Transaksi Hari Ini
        $transaksiHariIni = Transaksi::whereDate('created_at', $today)
            ->where('is_refunded', false)
            ->count();

        // 3. Omzet Bulan Ini
        $omzetBulanIni = (float) Transaksi::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('is_refunded', false)
            ->sum('total');

        // 4. Pembayaran Tunai vs Non-Tunai Hari Ini
        $tunaiHariIni = (float) Transaksi::whereDate('created_at', $today)
            ->where('is_refunded', false)
            ->where(function ($q) {
                $q->where('payment_method', 'like', '%Tunai%')
                    ->orWhere('payment_method', 'like', '%cash%');
            })
            ->sum('total');
        $nonTunaiHariIni = max(0, $omzetHariIni - $tunaiHariIni);

        // 5. Kasir Bertugas / Staf Masuk
        $kasirAktif = SesiKasir::where('status', 'Buka')->distinct('user_id')->count('user_id');
        $stafMasuk = Absensi::where('type', 'Masuk')->whereDate('created_at', $today)->count();
        $kasirLabel = $kasirAktif > 0 ? "{$kasirAktif} Kasir Aktif" : ($stafMasuk > 0 ? "{$stafMasuk} Staf Masuk" : '0 Shift Aktif');

        // 6. Stok Menipis
        $stokMenipisCount = Produk::where('is_active', true)->where('stock', '<=', 5)->count();
        $stokMenipisItems = Produk::where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get(['id', 'name', 'stock', 'price', 'category'])
            ->toArray();

        // 7. Pengeluaran / Belanja Hari Ini
        $pengeluaranHariIni = (float) Pengeluaran::whereDate('tanggal', $today)->sum('nominal');

        // 8. Transaksi Terkini
        $transaksiTerkini = Transaksi::latest()
            ->take(7)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'invoice_number' => $t->invoice_number,
                    'customer_name' => $t->customer_name ?: 'Pelanggan Walk-In',
                    'order_type' => $t->order_type === 'take_away' ? 'Take Away' : 'Dine In',
                    'waktu' => $t->created_at->format('H:i:s').' WIB',
                    'nominal' => (float) $t->total,
                    'nominal_rp' => 'Rp '.number_format($t->total, 0, ',', '.'),
                    'status' => $t->is_refunded ? 'Refund' : 'Berhasil',
                    'is_refunded' => (bool) $t->is_refunded,
                    'payment_method' => $t->payment_method ?: 'Tunai',
                ];
            })
            ->toArray();

        // 9. Produk Terlaris Hari Ini
        $todayOrders = Transaksi::whereDate('created_at', $today)
            ->where('is_refunded', false)
            ->get(['items']);

        $salesCount = [];
        foreach ($todayOrders as $order) {
            $items = is_array($order->items) ? $order->items : json_decode($order->items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    $name = $item['name'] ?? 'Menu';
                    $qty = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
                    $salesCount[$name] = ($salesCount[$name] ?? 0) + $qty;
                }
            }
        }
        arsort($salesCount);
        $topProduk = [];
        foreach (array_slice($salesCount, 0, 5, true) as $prodName => $qtySold) {
            $topProduk[] = [
                'name' => $prodName,
                'qty' => $qtySold,
            ];
        }

        // 10. Total Produk Aktif
        $totalProduk = Produk::where('is_active', true)->count();

        return [
            'omzetHariIni' => $omzetHariIni,
            'omzetHariIniFormatted' => 'Rp '.number_format($omzetHariIni, 0, ',', '.'),
            'transaksiHariIni' => $transaksiHariIni,
            'omzetBulanIni' => $omzetBulanIni,
            'omzetBulanIniFormatted' => 'Rp '.number_format($omzetBulanIni, 0, ',', '.'),
            'tunaiHariIni' => $tunaiHariIni,
            'tunaiHariIniFormatted' => 'Rp '.number_format($tunaiHariIni, 0, ',', '.'),
            'nonTunaiHariIni' => $nonTunaiHariIni,
            'nonTunaiHariIniFormatted' => 'Rp '.number_format($nonTunaiHariIni, 0, ',', '.'),
            'kasirBertugas' => $kasirLabel,
            'kasirAktifCount' => $kasirAktif,
            'stafMasukCount' => $stafMasuk,
            'stokMenipis' => $stokMenipisCount,
            'stokMenipisItems' => $stokMenipisItems,
            'pengeluaranHariIni' => $pengeluaranHariIni,
            'pengeluaranHariIniFormatted' => 'Rp '.number_format($pengeluaranHariIni, 0, ',', '.'),
            'transaksiTerkini' => $transaksiTerkini,
            'topProduk' => $topProduk,
            'totalProduk' => $totalProduk,
            'lastUpdated' => Carbon::now()->format('H:i:s').' WIB',
            'todayDate' => Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y'),
        ];
    }
}
