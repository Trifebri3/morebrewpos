<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AktivitasController extends Controller
{
    public function index(\App\Services\Admin\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Log Aktivitas Sistem';
        
        $absensis = \App\Models\Absensi::with('user')->latest()->take(50)->get()->map(function($item) {
            return [
                'type' => 'absensi',
                'title' => 'Absensi ' . $item->type,
                'description' => $item->user->name . ' melakukan absen ' . $item->type . ' (' . $item->status . ')',
                'date' => $item->created_at,
                'user' => $item->user->name,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                'color' => '#10b981'
            ];
        });
        
        $transaksis = \App\Models\Transaksi::with('user')->latest()->take(50)->get()->map(function($item) {
            return [
                'type' => 'transaksi',
                'title' => 'Transaksi Baru (' . $item->order_type . ')',
                'description' => 'Pesanan Rp ' . number_format($item->total_amount, 0, ',', '.') . ' oleh ' . ($item->user ? $item->user->name : 'Sistem'),
                'date' => $item->created_at,
                'user' => $item->user ? $item->user->name : 'Sistem',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>',
                'color' => '#3b82f6'
            ];
        });
        
        $pengeluarans = \App\Models\Pengeluaran::with('user')->latest()->take(50)->get()->map(function($item) {
            return [
                'type' => 'pengeluaran',
                'title' => 'Pengeluaran: ' . $item->title,
                'description' => 'Tercatat pengeluaran Rp ' . number_format($item->amount, 0, ',', '.') . ' oleh ' . ($item->user ? $item->user->name : 'Sistem'),
                'date' => $item->created_at,
                'user' => $item->user ? $item->user->name : 'Sistem',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>',
                'color' => '#ef4444'
            ];
        });

        $activities = collect($absensis)
            ->merge($transaksis)
            ->merge($pengeluarans)
            ->sortByDesc('date')
            ->take(100);
            
        return view('admin.staff.aktivitas.index', array_merge($data, [
            'activities' => $activities
        ]));
    }
}
