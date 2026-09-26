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
    public function penjualan(\App\Services\Admin\DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Laporan Penjualan & Stok';
        
        $type = $request->query('type', 'harian');
        $query = \App\Models\Transaksi::query();
        
        if ($type === 'harian') {
            $query->whereDate('created_at', today());
            $data['subtitle'] = 'Hari Ini (' . today()->format('d M Y') . ')';
        } elseif ($type === 'bulanan') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            $data['subtitle'] = 'Bulan ' . now()->format('F Y');
        } elseif ($type === 'pertanggal') {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00', 
                    $request->end_date . ' 23:59:59'
                ]);
                $data['subtitle'] = 'Periode: ' . $request->start_date . ' s/d ' . $request->end_date;
            } else {
                $query->whereDate('created_at', today());
                $data['subtitle'] = 'Pilih Rentang Tanggal';
            }
        } elseif ($type === 'lengkap') {
            $data['subtitle'] = 'Rekapan Keseluruhan';
        }
        
        $transaksis = $query->latest()->get();
        
        $itemsSold = [];
        foreach ($transaksis as $t) {
            $items = is_string($t->items) ? json_decode($t->items, true) : $t->items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $name = $item['name'] ?? 'Unknown';
                    $qty = $item['qty'] ?? 1;
                    $itemsSold[$name] = ($itemsSold[$name] ?? 0) + $qty;
                }
            }
        }
        arsort($itemsSold);
        
        return view('admin.laporan.penjualan', compact('data', 'transaksis', 'type', 'itemsSold'));
    }
}
