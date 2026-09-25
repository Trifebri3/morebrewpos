<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function pengeluaran(\App\Services\Admin\DashboardService $dashboardService, Request $request)
    {
        $dashboardData = $dashboardService->getDashboardData();
        
        // Group by date for daily report
        $laporanHarian = \App\Models\Pengeluaran::selectRaw('DATE(tanggal) as tanggal_harian, SUM(nominal) as total_pengeluaran, COUNT(*) as jumlah_transaksi')
            ->where('status', 'approved') // Hanya yang sudah disetujui
            ->groupBy('tanggal_harian')
            ->orderBy('tanggal_harian', 'desc')
            ->get();
            
        return view('admin.laporan.pengeluaran', array_merge($dashboardData, compact('laporanHarian')));
    }
}
