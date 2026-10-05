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
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_meter' => 'required|integer|min:5',
            'wifi_ssid' => 'nullable|string|max:255',
            'wifi_password' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $kedai = Kedai::first();
        if ($kedai) {
            $dataToUpdate = $request->only([
                'name', 'phone', 'address', 'latitude', 'longitude', 
                'radius_meter', 'wifi_ssid', 'wifi_password', 'instagram'
            ]);
            $dataToUpdate['is_qr_absen_enabled'] = $request->has('is_qr_absen_enabled');
            $kedai->update($dataToUpdate);

            // Handle logo replacement if uploaded
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $file->move(public_path(), 'logo.png');
            }
        }

        return back()->with('success', 'Pengaturan Kedai, Koordinat GPS, dan Profil berhasil diperbarui.');
    }
}
