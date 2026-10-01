<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(\App\Services\Admin\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Absensi Karyawan & Pengaturan';
        
        $absensis = \App\Models\Absensi::whereDate('created_at', \Carbon\Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();
            
        $kedai = \App\Models\Kedai::first();
        $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
        
        // Dapatkan shift hari ini
        $shiftsHariIni = \App\Models\Shift::with(['users'])->where('hari', $hariIni)->get();
        
        $recap = [
            'total_karyawan' => 0,
            'hadir' => 0,
            'terlambat' => 0,
            'belum_absen' => 0,
            'shifts' => []
        ];

        foreach ($shiftsHariIni as $shift) {
            $shiftRecap = [
                'name' => $shift->nama_shift,
                'jam' => $shift->jam_mulai . ' - ' . $shift->jam_selesai,
                'total_karyawan' => $shift->users->count(),
                'hadir' => 0,
                'terlambat' => 0
            ];
            
            $recap['total_karyawan'] += $shift->users->count();
            
            foreach ($shift->users as $u) {
                $absenUser = $absensis->where('user_id', $u->id)->where('type', 'Masuk')->first();
                if ($absenUser) {
                    $shiftRecap['hadir']++;
                    $recap['hadir']++;
                    if (str_contains($absenUser->status, 'Terlambat')) {
                        $shiftRecap['terlambat']++;
                        $recap['terlambat']++;
                    }
                }
            }
            $recap['shifts'][] = $shiftRecap;
        }
        $recap['belum_absen'] = $recap['total_karyawan'] - $recap['hadir'];

        return view('admin.staff.absensi.index', compact('data', 'absensis', 'kedai', 'recap'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $user = \App\Models\User::where('qr_code', $request->qr_code)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'QR Code tidak valid atau karyawan tidak ditemukan!'], 404);
        }

        // Cek absensi hari ini
        $absenHariIni = \App\Models\Absensi::where('user_id', $user->id)
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->get();

        $tipe = 'Masuk';
        if ($absenHariIni->count() == 1) {
            $tipe = 'Keluar';
        } elseif ($absenHariIni->count() >= 2) {
            return response()->json(['status' => 'error', 'message' => 'Karyawan ' . $user->name . ' sudah absen Masuk dan Keluar hari ini!'], 400);
        }

        // Hitung Keterlambatan (Opsional: berbasis Shift)
        $status = 'Tepat Waktu';
        $terlambatMenit = 0;

        if ($tipe == 'Masuk') {
            // Cari shift untuk karyawan ini hari ini
            $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
            $shift = \App\Models\Shift::where('hari', $hariIni)
                ->whereHas('users', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->first();

            if ($shift && !$shift->is_libur) {
                $waktuMulai = \Carbon\Carbon::createFromFormat('H:i:s', $shift->jam_mulai);
                $sekarang = \Carbon\Carbon::now();
                if ($sekarang->gt($waktuMulai)) {
                    $terlambatMenit = $sekarang->diffInMinutes($waktuMulai);
                    $status = 'Terlambat ' . $terlambatMenit . ' Menit';
                }
            }
        }

        $absen = \App\Models\Absensi::create([
            'user_id' => $user->id,
            'type' => $tipe,
            'status' => $status
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Absen ' . $tipe . ' berhasil untuk ' . $user->name,
            'data' => [
                'name' => $user->name,
                'type' => $tipe,
                'status' => $status,
                'time' => $absen->created_at->format('H:i:s')
            ]
        ]);
    }
    
    public function updateSettings(Request $request)
    {
        $kedai = \App\Models\Kedai::first();
        if ($kedai) {
            $kedai->update([
                'is_qr_absen_enabled' => $request->has('is_qr_absen_enabled'),
                'is_link_absen_enabled' => $request->has('is_link_absen_enabled'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meter' => $request->radius_meter ?? 50
            ]);
        }
        
        return back()->with('success', 'Pengaturan tipe absensi berhasil disimpan.');
    }
}
