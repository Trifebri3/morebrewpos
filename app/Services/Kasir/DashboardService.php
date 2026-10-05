<?php

namespace App\Services\Kasir;
use App\Models\User;

class DashboardService
{
    public function getDashboardData(): array
    {
        $dummyUser = User::where('role', 'kasir')->first();
        
        $user = auth()->user();
        
        $query = \App\Models\Produk::where('is_active', true)->orderBy('name');
        
        if ($user->kedai_id) {
            $query->where('kedai_id', $user->kedai_id);
        }
        
        $products = $query->get();
        $kedai = \App\Models\Kedai::first();
        
        return [
            'role' => 'Kasir',
            'dummyUser' => $dummyUser ? $dummyUser->name : 'Kasir',
            'kedai' => $kedai,
            'navGroups' => [
                'Utama' => [
                    ['label' => 'POS / Transaksi Baru', 'url' => route('kasir.dashboard'), 'active' => request()->routeIs('kasir.dashboard')],
                    ['label' => 'Riwayat Invoice', 'url' => route('kasir.invoice'), 'active' => request()->routeIs('kasir.invoice')],
                ],
                'Karyawan' => [
                    ['label' => 'Clock In / Absensi', 'url' => route('kasir.absensi'), 'active' => request()->routeIs('kasir.absensi')],
                    ['label' => 'Jadwal Shift Saya', 'url' => route('kasir.shift_jadwal'), 'active' => request()->routeIs('kasir.shift_jadwal')],
                ],
                'Operasional' => [
                    ['label' => 'Status Meja', 'url' => route('kasir.meja'), 'active' => request()->routeIs('kasir.meja')],
                    ['label' => 'Tutup Kasir (End of Day)', 'url' => route('kasir.tutup'), 'active' => request()->routeIs('kasir.tutup')],
                ],
                'Laporan' => [
                    ['label' => 'Laporan Penjualan', 'url' => route('kasir.laporan.penjualan'), 'active' => request()->routeIs('kasir.laporan.penjualan')],
                    ['label' => 'Laporan Pengeluaran', 'url' => route('kasir.laporan.pengeluaran'), 'active' => request()->routeIs('kasir.laporan.pengeluaran')],
                    ['label' => 'Laporan Shift', 'url' => route('kasir.laporan.shift'), 'active' => request()->routeIs('kasir.laporan.shift')],
                ],
                'Pengaturan' => [
                    ['label' => 'Pengaturan Kedai & Pajak', 'url' => route('kasir.pengaturan'), 'active' => request()->routeIs('kasir.pengaturan*')],
                ],
            ],
            'products' => $products,
            'vouchers' => \App\Models\Voucher::where('status', true)->get(),
            'tables' => \App\Models\Meja::all(),
        ];
    }
}
