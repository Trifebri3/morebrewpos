<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kedai;

class KedaiController extends Controller
{
    public function pengaturan()
    {
        $kedai = Kedai::first();
        return view('admin.kedai.pengaturan', compact('kedai'));
    }

    public function updatePengaturan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_meter' => 'required|integer|min:10',
        ]);

        $kedai = Kedai::first();
        if ($kedai) {
            $kedai->update($request->only('name', 'latitude', 'longitude', 'radius_meter'));
        }

        return back()->with('success', 'Pengaturan Kedai berhasil disimpan.');
    }
}
