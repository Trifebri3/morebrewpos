<?php

namespace App\Services\Superadmin;

use App\Models\Kedai;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function getDashboardData(): array
    {
        $dummyUser = User::where('role', 'superadmin')->first();

        $totalKedai = Kedai::where('is_active', true)->count();
        $totalOmzetHariIni = (float) Transaksi::whereDate('created_at', Carbon::today())->where('is_refunded', false)->sum('total');
        $totalTransaksi = Transaksi::where('is_refunded', false)->count();
        $stokMenipis = Produk::where('is_active', true)->where('stock', '<=', 5)->count();

        $latestActivities = Transaksi::latest()->take(5)->get()->map(function ($t) {
            return "[Penjualan] Transaksi #{$t->invoice_number} senilai Rp ".number_format($t->total, 0, ',', '.').' ('.($t->payment_method ?: 'Tunai').') - '.$t->created_at->format('H:i').' WIB';
        })->toArray();

        return [
            'role' => 'Superadmin',
            'dummyUser' => $dummyUser ? $dummyUser->name : 'Superadmin',
            'totalKedai' => $totalKedai,
            'totalOmzetHariIni' => 'Rp '.number_format($totalOmzetHariIni, 0, ',', '.'),
            'totalTransaksi' => $totalTransaksi,
            'stokMenipis' => $stokMenipis,
            'latestActivities' => $latestActivities,
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
        ];
    }
}
