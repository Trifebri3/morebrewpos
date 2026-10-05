<?php

namespace App\Services\Superadmin;

use App\Models\User;

class DashboardService
{
    public function getDashboardData(): array
    {
        $dummyUser = User::where('role', 'superadmin')->first();

        return [
            'role' => 'Superadmin',
            'dummyUser' => $dummyUser ? $dummyUser->name : 'Superadmin',
            'navGroups' => [
                'Main Menu' => [
                    ['label' => 'Dashboard', 'url' => route('superadmin.dashboard'), 'active' => request()->routeIs('superadmin.dashboard')],
                ],
                'Manajemen Kedai' => [
                    ['label' => 'Data Kedai', 'url' => route('superadmin.kedai.index'), 'active' => request()->routeIs('superadmin.kedai.*') && ! request()->routeIs('superadmin.kedai.performa')],
                    ['label' => 'Performa Kedai', 'url' => route('superadmin.kedai.performa'), 'active' => request()->routeIs('superadmin.kedai.performa')],
                ],
                'Manajemen Akun' => [
                    ['label' => 'Akun Admin & Kasir', 'url' => route('superadmin.akun.index'), 'active' => request()->routeIs('superadmin.akun.index')],
                    ['label' => 'Pengaturan Role', 'url' => route('superadmin.akun.roles'), 'active' => request()->routeIs('superadmin.akun.roles')],
                ],
                'Laporan & Keuangan' => [
                    ['label' => 'Seluruh Transaksi', 'url' => route('superadmin.laporan.transaksi'), 'active' => request()->routeIs('superadmin.laporan.transaksi')],
                    ['label' => 'Omzet Keseluruhan', 'url' => route('superadmin.laporan.omzet'), 'active' => request()->routeIs('superadmin.laporan.omzet')],
                    ['label' => 'Laporan Lengkap', 'url' => route('superadmin.laporan.lengkap'), 'active' => request()->routeIs('superadmin.laporan.lengkap')],
                ],
                'Monitoring' => [
                    ['label' => 'Monitoring Stok', 'url' => route('superadmin.monitoring.stok'), 'active' => request()->routeIs('superadmin.monitoring.stok')],
                    ['label' => 'Monitoring Kas', 'url' => route('superadmin.monitoring.kas'), 'active' => request()->routeIs('superadmin.monitoring.kas')],
                    ['label' => 'Monitoring Absensi', 'url' => route('superadmin.monitoring.absensi'), 'active' => request()->routeIs('superadmin.monitoring.absensi')],
                    ['label' => 'Aktivitas User & Audit Log', 'url' => route('superadmin.monitoring.audit'), 'active' => request()->routeIs('superadmin.monitoring.audit')],
                ],
                'Pengaturan Global' => [
                    ['label' => 'Produk Global', 'url' => route('superadmin.pengaturan.produk'), 'active' => request()->routeIs('superadmin.pengaturan.produk')],
                    ['label' => 'Pajak & Service Charge', 'url' => route('superadmin.pengaturan.pajak'), 'active' => request()->routeIs('superadmin.pengaturan.pajak')],
                    ['label' => 'Sistem QR', 'url' => route('superadmin.pengaturan.qr'), 'active' => request()->routeIs('superadmin.pengaturan.qr')],
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
