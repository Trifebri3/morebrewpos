<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kedai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AbsensiController extends Controller
{
    public function showPublicForm()
    {
        $kedai = Kedai::first();
        if (!$kedai) {
            return "Pengaturan Kedai belum dilakukan.";
        }
        
        if (!$kedai->is_link_absen_enabled) {
            return "Fitur Absensi via Tautan saat ini dinonaktifkan oleh Admin.";
        }

        return view('absen', compact('kedai'));
    }

    public function submitPublicAbsen(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'type' => 'required|in:Masuk,Keluar',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string',
        ]);

        $kedai = Kedai::first();
        if (!$kedai || !$kedai->is_link_absen_enabled) {
            return response()->json(['success' => false, 'message' => 'Fitur absensi via tautan saat ini dinonaktifkan oleh Admin.']);
        }
        
        // Cari user berdasarkan email atau qr_code (atau ID)
        $user = User::where('email', $request->identifier)
                    ->orWhere('qr_code', $request->identifier)
                    ->orWhere('id', $request->identifier)
                    ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Identitas Karyawan tidak ditemukan.']);
        }

        // Cek absensi hari ini (Mencegah absen ganda)
        $absenHariIni = \App\Models\Absensi::where('user_id', $user->id)
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->where('type', $request->type)
            ->exists();
            
        if ($absenHariIni) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen ' . $request->type . ' hari ini.']);
        }

        // Cek Jarak (Haversine Formula) - jika latitude/longitude kedai di-set
        $status = 'Tepat Waktu';
        if ($kedai->latitude && $kedai->longitude) {
            $distance = $this->calculateDistance($kedai->latitude, $kedai->longitude, $request->latitude, $request->longitude);
            if ($distance > $kedai->radius_meter) {
                $status = 'Luar Zona Kedai (' . round($distance) . 'm)';
            }
        }

        // Hitung Keterlambatan berbasis Shift (jika tipe = Masuk)
        if ($request->type == 'Masuk' && !str_contains($status, 'Luar Zona')) {
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

        // Proses Foto (Base64 ke File)
        $photoData = $request->photo;
        list($type, $photoData) = explode(';', $photoData);
        list(, $photoData)      = explode(',', $photoData);
        $photoData = base64_decode($photoData);
        $fileName = 'absensi/' . $user->id . '_' . time() . '.png';
        Storage::disk('public')->put($fileName, $photoData);

        // Simpan
        Absensi::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_path' => $fileName,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Berhasil Absen ' . $request->type . '! Selamat bekerja, ' . $user->name,
            'status' => $status
        ]);
    }

    public function checkUser(Request $request)
    {
        $request->validate(['identifier' => 'required']);
        $user = User::where('email', $request->identifier)
                    ->orWhere('qr_code', $request->identifier)
                    ->orWhere('id', $request->identifier)
                    ->first();

        if ($user) {
            return response()->json(['success' => true, 'name' => $user->name]);
        }

        return response()->json(['success' => false]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // in meters
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}
