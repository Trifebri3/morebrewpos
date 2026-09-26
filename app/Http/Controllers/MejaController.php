<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Kedai;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MejaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(\App\Services\Admin\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Manajemen Meja';
        $mejas = Meja::all();
        return view('admin.meja.index', compact('data', 'mejas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $kedai = Kedai::first();
        if (!$kedai) {
            return back()->with('error', 'Kedai belum diatur.');
        }

        Meja::create([
            'kedai_id' => $kedai->id,
            'name' => $request->name,
            'qr_hash' => \Illuminate\Support\Str::random(10),
        ]);

        return redirect()->route('admin.kedai.meja.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Meja $meja)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meja $meja)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meja $meja)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $meja->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.kedai.meja.index')->with('success', 'Meja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meja $meja)
    {
        $meja->delete();
        return redirect()->route('admin.kedai.meja.index')->with('success', 'Meja berhasil dihapus.');
    }

    public function downloadQr(Meja $meja)
    {
        $url = url('/order?meja=' . $meja->qr_hash);
        $qrCode = QrCode::size(300)->generate($url);
        
        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml');
    }
}
