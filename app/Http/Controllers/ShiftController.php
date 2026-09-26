<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(\App\Services\Admin\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Jadwal Shift Mingguan';
        
        $shifts = \App\Models\Shift::with('users')->get();
        // Kelompokkan berdasarkan hari
        $groupedShifts = $shifts->groupBy('hari');
        
        $karyawans = \App\Models\User::where('role', 'staff')->get();
        
        $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.staff.shift.index', compact('data', 'groupedShifts', 'haris', 'karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string',
            'nama' => 'nullable|string|max:255',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'is_libur' => 'nullable|boolean',
        ]);

        \App\Models\Shift::create([
            'hari' => $request->hari,
            'nama' => $request->nama,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'is_libur' => $request->has('is_libur') ? true : false,
        ]);

        return back()->with('success', 'Sesi shift berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $shift = \App\Models\Shift::findOrFail($id);
        $shift->delete();
        return back()->with('success', 'Sesi shift berhasil dihapus.');
    }

    public function assignUser(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $shift = \App\Models\Shift::findOrFail($id);
        
        // Prevent duplicate assignment
        if (!$shift->users->contains($request->user_id)) {
            $shift->users()->attach($request->user_id);
        }

        return back()->with('success', 'Karyawan berhasil ditugaskan ke sesi ini.');
    }

    public function removeUser($shift_id, $user_id)
    {
        $shift = \App\Models\Shift::findOrFail($shift_id);
        $shift->users()->detach($user_id);

        return back()->with('success', 'Karyawan dihapus dari sesi ini.');
    }
}
