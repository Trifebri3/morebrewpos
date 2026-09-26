<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Services\Admin\DashboardService;

class PenjualanController extends Controller
{
    public function invoice(DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Daftar Invoice & Transaksi';

        $type = $request->query('type', 'lengkap');
        $query = Transaksi::query();
        
        if ($type === 'harian') {
            $query->whereDate('created_at', today());
            $data['subtitle'] = 'Invoice Hari Ini (' . today()->format('d M Y') . ')';
        } elseif ($type === 'mingguan') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            $data['subtitle'] = 'Invoice Minggu Ini (' . now()->startOfWeek()->format('d M') . ' - ' . now()->endOfWeek()->format('d M Y') . ')';
        } elseif ($type === 'bulanan') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            $data['subtitle'] = 'Invoice Bulan ' . now()->format('F Y');
        } elseif ($type === 'pertanggal') {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00', 
                    $request->end_date . ' 23:59:59'
                ]);
                $data['subtitle'] = 'Periode: ' . date('d M Y', strtotime($request->start_date)) . ' s/d ' . date('d M Y', strtotime($request->end_date));
            } else {
                $query->whereDate('created_at', today());
                $data['subtitle'] = 'Pilih Rentang Tanggal';
            }
        } else {
            $data['subtitle'] = 'Berikut adalah semua data invoice transaksi dari kasir beserta rekapan totalnya.';
        }

        $transaksis = $query->latest()->get();
        
        $totalPemasukan = $transaksis->sum('total');
        $totalTransaksi = $transaksis->count();
        
        return view('admin.penjualan.invoice', compact('data', 'transaksis', 'totalPemasukan', 'totalTransaksi', 'type'));
    }

    public function riwayat(DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Riwayat Transaksi Detail';
        $data['subtitle'] = 'Riwayat lengkap setiap barang yang dibeli oleh pelanggan di kasir.';

        // Ambil data transaksi dengan paginasi agar tidak berat
        $transaksis = Transaksi::latest()->paginate(10);
        
        return view('admin.penjualan.riwayat', compact('data', 'transaksis'));
    }

    public function transaksi(DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Semua Transaksi Masuk';
        $data['subtitle'] = 'Pantau seluruh transaksi terbaru secara real-time yang terjadi di kasir.';

        $transaksis = Transaksi::latest()->paginate(15);
        
        return view('admin.penjualan.transaksi', compact('data', 'transaksis'));
    }

    public function refund(DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Kelola Refund Transaksi';
        $data['subtitle'] = 'Daftar transaksi yang dikembalikan atau dibatalkan.';

        $refunds = Transaksi::where('is_refunded', true)->latest()->paginate(15);
        
        return view('admin.penjualan.refund', compact('data', 'refunds'));
    }

    public function processRefund(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'refund_reason' => 'required|string|max:255'
        ]);

        if ($transaksi->is_refunded) {
            return back()->with('error', 'Transaksi sudah di-refund sebelumnya.');
        }

        $transaksi->update([
            'is_refunded' => true,
            'refund_reason' => $request->refund_reason,
            'refunded_at' => now(),
        ]);

        // Opsional: kembalikan stok produk yang di-refund
        if (is_array($transaksi->items)) {
            foreach ($transaksi->items as $item) {
                if (isset($item['id']) && isset($item['qty'])) {
                    $produk = \App\Models\Produk::find($item['id']);
                    if ($produk) {
                        $produk->stock += $item['qty'];
                        $produk->save();
                    }
                }
            }
        }

        return back()->with('success', 'Berhasil memproses refund untuk Invoice ' . $transaksi->invoice_number);
    }
}
