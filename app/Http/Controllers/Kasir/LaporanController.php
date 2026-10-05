<?php

namespace App\Http\Controllers\Kasir;

use App\Exports\PengeluaranExport;
use App\Exports\PenjualanExport;
use App\Exports\ShiftExport;
use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use App\Models\SesiKasir;
use App\Models\Transaksi;
use App\Services\Kasir\DashboardService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function penjualan(DashboardService $dashboardService, Request $request)
    {
        $dashboardData = $dashboardService->getDashboardData();

        $riwayat = Transaksi::whereDate('created_at', today())->orderBy('created_at', 'desc')->get();
        $totalPenjualan = $riwayat->sum('total');
        $pengeluaranList = Pengeluaran::whereDate('tanggal', today())->orderBy('created_at', 'desc')->get();
        $pengeluaran = $pengeluaranList->sum('nominal');

        // Cek sesi aktif kasir
        $activeSession = SesiKasir::where('user_id', auth()->id())
            ->where('status', 'buka')
            ->latest()
            ->first();

        $modalAwal = $activeSession ? $activeSession->modal_awal : 500000;
        $estimasiUangLaci = $modalAwal + $totalPenjualan - $pengeluaran;

        return view('kasir.laporan.penjualan', array_merge($dashboardData, compact(
            'riwayat', 'totalPenjualan', 'pengeluaran', 'pengeluaranList', 'modalAwal', 'estimasiUangLaci'
        )));
    }

    public function exportPenjualan()
    {
        return Excel::download(
            new PenjualanExport('harian'),
            'laporan_penjualan_kasir_'.date('YmdHis').'.xlsx'
        );
    }

    public function pengeluaran(DashboardService $dashboardService)
    {
        $dashboardData = $dashboardService->getDashboardData();

        $riwayat = Pengeluaran::whereDate('tanggal', today())->orderBy('created_at', 'desc')->get();
        $totalPengeluaran = $riwayat->sum('nominal');
        $budget = 1000000;
        $sisaBudget = $budget - $totalPengeluaran;

        return view('kasir.laporan.pengeluaran', array_merge($dashboardData, compact('riwayat', 'totalPengeluaran', 'budget', 'sisaBudget')));
    }

    public function exportPengeluaran()
    {
        return Excel::download(
            new PengeluaranExport(today()->format('Y-m-d'), today()->format('Y-m-d')),
            'laporan_pengeluaran_kasir_'.date('YmdHis').'.xlsx'
        );
    }

    public function shift(DashboardService $dashboardService)
    {
        $dashboardData = $dashboardService->getDashboardData();

        // Ambil riwayat sesi kasir yang login
        $sesis = SesiKasir::where('user_id', auth()->id())
            ->latest('waktu_buka')
            ->get();

        $activeSession = $sesis->firstWhere('status', 'buka');

        return view('kasir.laporan.shift', array_merge($dashboardData, compact('sesis', 'activeSession')));
    }

    public function exportShift()
    {
        return Excel::download(
            new ShiftExport,
            'laporan_shift_kasir_'.date('YmdHis').'.xlsx'
        );
    }
}
