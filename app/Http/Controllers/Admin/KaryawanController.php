<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(\App\Services\Admin\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Manajemen Karyawan';
        
        // Hanya tampilkan staff (karyawan), sembunyikan admin, kasir, dan superadmin
        $karyawans = \App\Models\User::where('role', 'staff')->orderBy('id', 'desc')->get();
        return view('admin.staff.karyawan.index', compact('data', 'karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $data = $request->except('photo');
        $data['role'] = 'staff';
        $data['qr_code'] = \Illuminate\Support\Str::random(10);
        $data['kedai_id'] = 1; // Assuming kedai_id 1 is default

        // Staff does not login
        $data['email'] = 'staff_' . time() . '@no-login.com';
        $data['password'] = bcrypt(\Illuminate\Support\Str::random(16));

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('karyawan', 'public');
        }

        \App\Models\User::create($data);

        return back()->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $karyawan = \App\Models\User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $data = $request->except('photo');
        
        if ($request->hasFile('photo')) {
            if ($karyawan->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($karyawan->photo);
            }
            $data['photo'] = $request->file('photo')->store('karyawan', 'public');
        }

        $karyawan->update($data);

        return back()->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $karyawan = \App\Models\User::findOrFail($id);
        if ($karyawan->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($karyawan->photo);
        }
        $karyawan->delete();

        return back()->with('success', 'Karyawan berhasil dihapus.');
    }

    public function qr($id)
    {
        $karyawan = \App\Models\User::findOrFail($id);
        if (!$karyawan->qr_code) {
            $karyawan->update(['qr_code' => \Illuminate\Support\Str::random(10)]);
        }
        $qrData = $karyawan->qr_code;
        return response(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate($qrData))
            ->header('Content-Type', 'image/svg+xml');
    }
}
