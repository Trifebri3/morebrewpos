<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function penjualan(\App\Services\Kasir\DashboardService $dashboardService)
    {
        $dashboardData = $dashboardService->getDashboardData();
        
        $riwayat = \App\Models\Transaksi::whereDate('created_at', today())->orderBy('created_at', 'desc')->get();
        $totalPenjualan = $riwayat->sum('total');
        $pengeluaranList = \App\Models\Pengeluaran::whereDate('tanggal', today())->orderBy('created_at', 'desc')->get();
        $pengeluaran = $pengeluaranList->sum('nominal');
        $modalAwal = 500000;
        $estimasiUangLaci = $modalAwal + $totalPenjualan - $pengeluaran;

        return view('kasir.laporan.penjualan', array_merge($dashboardData, compact('riwayat', 'totalPenjualan', 'pengeluaran', 'pengeluaranList', 'modalAwal', 'estimasiUangLaci')));
    }

    public function pengeluaran(\App\Services\Kasir\DashboardService $dashboardService)
    {
        $dashboardData = $dashboardService->getDashboardData();
        
        // Mengambil data riwayat pengeluaran dari database (jika ada model Pengeluaran)
        // Sementara kita pakai data dummy atau data asli jika ada
        $riwayat = \App\Models\Pengeluaran::whereDate('tanggal', today())->orderBy('created_at', 'desc')->get() ?? [];
        $totalPengeluaran = $riwayat->sum('nominal');
        // Asumsi budget awal misal 1000000
        $budget = 1000000;
        $sisaBudget = $budget - $totalPengeluaran;

        return view('kasir.laporan.pengeluaran', array_merge($dashboardData, compact('riwayat', 'totalPengeluaran', 'budget', 'sisaBudget')));
    }

    public function shift(\App\Services\Kasir\DashboardService $dashboardService)
    {
        $dashboardData = $dashboardService->getDashboardData();
        return view('kasir.laporan.shift', $dashboardData);
    }
}
