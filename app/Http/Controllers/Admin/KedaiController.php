<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kedai;

class KedaiController extends Controller
{
    public function pengaturan(\App\Services\Admin\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Pengaturan Kedai';
        $kedai = Kedai::first();
        return view('admin.kedai.pengaturan', compact('data', 'kedai'));
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
            $dataToUpdate = $request->only('name', 'latitude', 'longitude', 'radius_meter');
            $dataToUpdate['is_qr_absen_enabled'] = $request->has('is_qr_absen_enabled');
            $kedai->update($dataToUpdate);
        }

        return back()->with('success', 'Pengaturan Kedai berhasil disimpan.');
    }
}
