<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;

class StokController extends Controller
{
    public function index()
    {
        $produks = Produk::orderBy('category')->orderBy('name')->get();
        return view('admin.stok.index', compact('produks'));
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
