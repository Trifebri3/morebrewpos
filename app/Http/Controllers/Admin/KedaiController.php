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
        $data['title'] = 'Pengaturan Kedai & Pajak';
        $kedai = Kedai::first();
        if (!$kedai) {
            $kedai = Kedai::create([
                'name' => 'MOREBREWW',
                'address' => 'Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung, Jawa Barat 40261',
                'phone' => '',
                'tax_percentage' => 11.00,
                'is_tax_enabled' => true,
                'tax_name' => 'PB1 (Pajak Restoran)',
                'budget_harian' => 1000000,
                'receipt_header' => 'something, between home and everywhere',
                'receipt_footer' => 'Silakan datang kembali!',
                'wifi_ssid' => 'moreandmore',
                'wifi_password' => 'bolehlihatsenyumnya?',
                'instagram' => '@morebrewcoffee',
            ]);
        }
        return view('admin.kedai.pengaturan', compact('data', 'kedai'));
    }

    public function pengaturanKasir(\App\Services\Kasir\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Pengaturan Kedai & Pajak';
        $kedai = Kedai::first();
        if (!$kedai) {
            $kedai = Kedai::create([
                'name' => 'MOREBREWW',
                'address' => 'Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung, Jawa Barat 40261',
                'phone' => '',
                'tax_percentage' => 11.00,
                'is_tax_enabled' => true,
                'tax_name' => 'PB1 (Pajak Restoran)',
                'budget_harian' => 1000000,
                'receipt_header' => 'something, between home and everywhere',
                'receipt_footer' => 'Silakan datang kembali!',
                'wifi_ssid' => 'moreandmore',
                'wifi_password' => 'bolehlihatsenyumnya?',
                'instagram' => '@morebrewcoffee',
            ]);
        }
        return view('admin.kedai.pengaturan', compact('data', 'kedai'));
    }

    public function updatePengaturan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'tax_name' => 'nullable|string|max:100',
            'budget_harian' => 'nullable|numeric|min:0',
            'receipt_header' => 'nullable|string|max:255',
            'receipt_footer' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_meter' => 'required|integer|min:5',
            'wifi_ssid' => 'nullable|string|max:255',
            'wifi_password' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $kedai = Kedai::first();
        if (!$kedai) {
            $kedai = new Kedai();
        }

        $dataToUpdate = $request->only([
            'name', 'phone', 'address', 'latitude', 'longitude', 
            'radius_meter', 'wifi_ssid', 'wifi_password', 'instagram',
            'tax_percentage', 'tax_name', 'budget_harian',
            'receipt_header', 'receipt_footer'
        ]);

        $dataToUpdate['is_tax_enabled'] = $request->has('is_tax_enabled');
        $dataToUpdate['is_qr_absen_enabled'] = $request->has('is_qr_absen_enabled');
        $dataToUpdate['is_link_absen_enabled'] = $request->has('is_link_absen_enabled');

        $kedai->fill($dataToUpdate);
        $kedai->save();

        // Handle logo replacement if uploaded
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $file->move(public_path(), 'logo.png');
        }

        return back()->with('success', 'Pengaturan Kedai, Pajak (PB1), Profil, dan Koordinat GPS berhasil diperbarui & tersinkronisasi!');
    }
}
