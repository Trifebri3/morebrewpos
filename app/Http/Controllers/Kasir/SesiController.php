<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SesiController extends Controller
{
    public function index(\App\Services\Kasir\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Manajemen Sesi Kasir';
        
        $sesiAktif = \App\Models\SesiKasir::where('user_id', auth()->id())
            ->whereIn('status', ['Buka', 'buka', 'open', 'OPEN'])
            ->latest('waktu_buka')
            ->first();
            
        $riwayatSesi = \App\Models\SesiKasir::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('kasir.sesi.index', array_merge($data, [
            'sesiAktif' => $sesiAktif,
            'riwayatSesi' => $riwayatSesi
        ]));
    }

    public function buka(Request $request)
    {
        $request->validate([
            'modal_awal' => 'required|numeric|min:0'
        ]);

        $cekSesi = \App\Models\SesiKasir::where('user_id', auth()->id())
            ->whereIn('status', ['Buka', 'buka', 'open', 'OPEN'])
            ->exists();
            
        if ($cekSesi) {
            return back()->with('error', 'Anda masih memiliki sesi kasir yang aktif!');
        }

        \App\Models\SesiKasir::create([
            'user_id' => auth()->id(),
            'waktu_buka' => now(),
            'modal_awal' => $request->modal_awal,
            'status' => 'buka'
        ]);

        return back()->with('success', 'Sesi kasir berhasil dibuka. Selamat bertugas!');
    }

    public function tutup(Request $request)
    {
        $request->validate([
            'uang_fisik' => 'required|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        $sesiAktif = \App\Models\SesiKasir::where('user_id', auth()->id())
            ->whereIn('status', ['Buka', 'buka', 'open', 'OPEN'])
            ->latest('waktu_buka')
            ->first();
            
        if (!$sesiAktif) {
            return back()->with('error', 'Tidak ada sesi aktif untuk ditutup.');
        }

        // Hitung total pendapatan selama sesi ini (dari transaksi user ini yg sukses/lunas)
        $totalPendapatan = \App\Models\Transaksi::where('user_id', auth()->id())
            ->where('status', 'lunas')
            ->where('created_at', '>=', $sesiAktif->waktu_buka)
            ->sum('total_amount');
            
        // Selisih = uang fisik - (modal awal + total pendapatan)
        $selisih = $request->uang_fisik - ($sesiAktif->modal_awal + $totalPendapatan);

        $sesiAktif->update([
            'waktu_tutup' => now(),
            'total_pendapatan' => $totalPendapatan,
            'uang_fisik' => $request->uang_fisik,
            'selisih' => $selisih,
            'catatan' => $request->catatan,
            'status' => 'Tutup'
        ]);

        return back()->with('success', 'Sesi kasir berhasil ditutup.');
    }
}
