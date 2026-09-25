<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    protected function getViewData($title = 'Kategori')
    {
        $dashboardService = app(\App\Services\Admin\DashboardService::class);
        $data = $dashboardService->getDashboardData();
        $data['title'] = $title;
        return $data;
    }

    public function index()
    {
        $data = $this->getViewData('Daftar Kategori');
        $kategoris = Kategori::where('kedai_id', auth()->user()->kedai_id)->latest()->get();
        return view('admin.kategori.index', compact('data', 'kategoris'));
    }

    public function create()
    {
        $data = $this->getViewData('Tambah Kategori');
        return view('admin.kategori.create', compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'prefix' => 'nullable|string|max:10',
        ]);

        $validated['kedai_id'] = auth()->user()->kedai_id;

        // Auto-generate prefix if empty
        if (empty($validated['prefix'])) {
            $validated['prefix'] = strtoupper(substr($validated['name'], 0, 3));
        } else {
            $validated['prefix'] = strtoupper($validated['prefix']);
        }

        Kategori::create($validated);
        return redirect()->route('admin.operasional.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        if ($kategori->kedai_id != auth()->user()->kedai_id) abort(403);
        $data = $this->getViewData('Edit Kategori');
        return view('admin.kategori.edit', compact('data', 'kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        if ($kategori->kedai_id != auth()->user()->kedai_id) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'prefix' => 'required|string|max:10',
        ]);
        $validated['prefix'] = strtoupper($validated['prefix']);

        $kategori->update($validated);
        return redirect()->route('admin.operasional.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->kedai_id != auth()->user()->kedai_id) abort(403);
        $kategori->delete();
        return redirect()->route('admin.operasional.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
