<?php

namespace App\Services\Admin;

use App\Models\User;

class DashboardService
{
    public function getDashboardData(): array
    {
        $dummyUser = User::where('role', 'admin')->first();

        return [
            'role' => 'Admin Kedai',
            'dummyUser' => $dummyUser ? $dummyUser->name : 'Admin',
            'navGroups' => [
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
                'Kedai' => [
                    ['label' => 'Meja', 'url' => route('admin.kedai.meja.index'), 'active' => request()->routeIs('admin.kedai.meja.*')],
                    ['label' => 'Pengaturan Kedai', 'url' => route('admin.kedai.pengaturan'), 'active' => request()->routeIs('admin.kedai.pengaturan')],
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
            ],
            'products' => [
                ['name' => 'Item 1', 'price' => '1.99'],
                ['name' => 'Item 2', 'price' => '2.69'],
                ['name' => 'Item 3', 'price' => '3.21'],
                ['name' => 'Item 4', 'price' => '0.99'],
                ['name' => 'Item 5', 'price' => '2.11'],
                ['name' => 'Item 6', 'price' => '0.23'],
                ['name' => 'Item 7', 'price' => '1.23'],
                ['name' => 'Item 8', 'price' => '4.21'],
            ],
        ];
    }
}
