<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\Kedai;

class PengeluaranController extends Controller
{
    protected function getViewData($title = 'Belanja Harian')
    {
        $dashboardService = app(\App\Services\Admin\DashboardService::class);
        $data = $dashboardService->getDashboardData();
        $data['title'] = $title;
        return $data;
    }

    public function index()
    {
        $data = $this->getViewData('Belanja Harian (Kas Kecil)');
        $kedaiId = auth()->user()->kedai_id;
        $kedai = Kedai::find($kedaiId);
        
        $pengeluarans = Pengeluaran::where('kedai_id', $kedaiId)
                        ->with('user', 'approver')
                        ->latest('tanggal')
                        ->latest('id')
                        ->get();
                        
        // Hitung total pending dan approved bulan ini
        $totalApproved = Pengeluaran::where('kedai_id', $kedaiId)
                            ->where('status', 'approved')
                            ->whereMonth('tanggal', date('m'))
                            ->sum('nominal');

        return view('admin.pengeluaran.index', compact('data', 'kedai', 'pengeluarans', 'totalApproved'));
    }

    public function updateBudget(Request $request)
    {
        $request->validate(['budget_harian' => 'required|numeric|min:0']);
        $kedai = Kedai::find(auth()->user()->kedai_id);
        $kedai->update(['budget_harian' => $request->budget_harian]);
        
        return back()->with('success', 'Batas Anggaran Harian berhasil diperbarui.');
    }

    public function approve(Pengeluaran $pengeluaran)
    {
        if ($pengeluaran->kedai_id != auth()->user()->kedai_id) abort(403);
        $pengeluaran->update([
            'status' => 'approved',
            'approved_by' => auth()->id()
        ]);
        return back()->with('success', 'Pengeluaran disetujui.');
    }

    public function reject(Pengeluaran $pengeluaran)
    {
        if ($pengeluaran->kedai_id != auth()->user()->kedai_id) abort(403);
        $pengeluaran->update([
            'status' => 'rejected',
            'approved_by' => auth()->id()
        ]);
        return back()->with('success', 'Pengeluaran ditolak.');
    }
}
