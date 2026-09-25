<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\Kedai;
use Carbon\Carbon;

class PengeluaranController extends Controller
{
    public function index()
    {
        $kedaiId = auth()->user()->kedai_id;
        $kedai = Kedai::find($kedaiId);
        
        // Total pending + approved hari ini
        $pengeluaranHariIni = Pengeluaran::where('kedai_id', $kedaiId)
            ->whereDate('tanggal', Carbon::today())
            ->whereIn('status', ['pending', 'approved'])
            ->sum('nominal');
            
        $sisaBudget = $kedai->budget_harian - $pengeluaranHariIni;
        
        $riwayat = Pengeluaran::where('kedai_id', $kedaiId)
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(10)
            ->get();
            
        // Pass dashboard data for sidebar
        $dashboardService = app(\App\Services\Kasir\DashboardService::class);
        $dashboardData = $dashboardService->getDashboardData();
        
        return view('kasir.pengeluaran', array_merge(compact('kedai', 'sisaBudget', 'riwayat'), $dashboardData));
    }

    public function store(Request $request)
    {
        $kedaiId = auth()->user()->kedai_id;
        $kedai = Kedai::find($kedaiId);

        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $pengeluaranHariIni = Pengeluaran::where('kedai_id', $kedaiId)
            ->whereDate('tanggal', Carbon::today())
            ->whereIn('status', ['pending', 'approved'])
            ->sum('nominal');
            
        if (($pengeluaranHariIni + $validated['nominal']) > $kedai->budget_harian) {
            return back()->with('error', 'Gagal! Nominal ini melebihi sisa batas anggaran harian kedai.');
        }

        $validated['kedai_id'] = $kedaiId;
        $validated['user_id'] = auth()->id();
        $validated['tanggal'] = Carbon::today();
        $validated['status'] = 'pending';

        if ($request->filled('bukti_base64')) {
            $image_parts = explode(";base64,", $request->bukti_base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1] ?? 'jpg';
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = uniqid() . '.jpg'; // compressed image is always exported as jpeg/jpg from canvas
            
            \Illuminate\Support\Facades\Storage::disk('public')->put('bukti_pengeluaran/' . $fileName, $image_base64);
            $validated['bukti'] = 'bukti_pengeluaran/' . $fileName;
        }

        Pengeluaran::create($validated);
        
        return back()->with('success', 'Pengeluaran berhasil dicatat dan menunggu persetujuan Admin.');
    }
}
