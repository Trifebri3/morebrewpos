<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;

use App\Services\Admin\DashboardService;

class StokController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();
        $data['title'] = 'Manajemen Stok Harian';
        
        // Filter produk berdasarkan kedai admin
        $kedaiId = auth()->user()->kedai_id;
        $produks = Produk::where('kedai_id', $kedaiId)->orderBy('category')->orderBy('name')->get();
        return view('admin.stok.index', compact('data', 'produks'));
    }

    public function update(Request $request)
    {
        $stocks = $request->input('stocks', []);
        
        foreach ($stocks as $id => $stockValue) {
            Produk::where('id', $id)->update(['stock' => (int) $stockValue]);
        }

        return redirect()->route('admin.operasional.stok')->with('success', 'Stok harian berhasil diperbarui.');
    }
}
