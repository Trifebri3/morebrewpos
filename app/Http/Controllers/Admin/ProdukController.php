<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Services\Admin\DashboardService;
use App\Exports\ProdukExport;
use App\Imports\ProdukImport;
use Maatwebsite\Excel\Facades\Excel;

class ProdukController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    private function getViewData($title)
    {
        $data = $this->dashboardService->getDashboardData();
        $data['title'] = $title;
        return $data;
    }

    public function index()
    {
        $data = $this->getViewData('Daftar Produk');
        $kedaiId = auth()->user()->kedai_id;
        $produks = Produk::where('kedai_id', $kedaiId)->latest()->get();
        return view('admin.produk.index', compact('data', 'produks'));
    }

    public function create()
    {
        $data = $this->getViewData('Tambah Produk');
        $kategoris = \App\Models\Kategori::where('kedai_id', auth()->user()->kedai_id)->get();
        return view('admin.produk.create', compact('data', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean'
        ]);

        $validated['kedai_id'] = auth()->user()->kedai_id;
        $validated['is_active'] = $request->has('is_active');

        // Auto generate SKU
        $categoryPrefix = $validated['category'] ? strtoupper(substr($validated['category'], 0, 3)) : 'PRD';
        $uniqueId = rand(1000, 9999);
        $validated['sku'] = $categoryPrefix . '-' . $uniqueId;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['stock'] = 100; // Set default stok 100

        Produk::create($validated);
        return redirect()->route('admin.operasional.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        if ($produk->kedai_id != auth()->user()->kedai_id) abort(403);
        $data = $this->getViewData('Edit Produk');
        $kategoris = \App\Models\Kategori::where('kedai_id', auth()->user()->kedai_id)->get();
        return view('admin.produk.edit', compact('data', 'produk', 'kategoris'));
    }

    public function update(Request $request, Produk $produk)
    {
        if ($produk->kedai_id != auth()->user()->kedai_id) abort(403);

        $validated = $request->validate([
            'sku' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($produk->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($produk->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $produk->update($validated);
        return redirect()->route('admin.operasional.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        if ($produk->kedai_id != auth()->user()->kedai_id) abort(403);
        if ($produk->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($produk->image);
        }
        $produk->delete();
        return redirect()->route('admin.operasional.produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new ProdukExport(auth()->user()->kedai_id), 'produk_'.date('YmdHis').'.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new ProdukImport(auth()->user()->kedai_id), $request->file('file'));
        return redirect()->route('admin.operasional.produk.index')->with('success', 'Data Produk berhasil diimpor.');
    }
}
