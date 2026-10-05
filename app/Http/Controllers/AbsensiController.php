<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kedai;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function showPublicForm()
    {
        $kedai = Kedai::first();
        if (! $kedai) {
            return 'Pengaturan Kedai belum dilakukan.';
        }

        if (! $kedai->is_link_absen_enabled) {
            return 'Fitur Absensi via Tautan saat ini dinonaktifkan oleh Admin.';
        }

        $karyawans = User::whereIn('role', ['staff', 'kasir', 'admin'])
            ->orderBy('name')
            ->get(['id', 'name', 'role', 'position', 'email']);

        return view('absen', compact('kedai', 'karyawans'));
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
        if (! $kedai || ! $kedai->is_link_absen_enabled) {
            return response()->json(['success' => false, 'message' => 'Fitur absensi via tautan saat ini dinonaktifkan oleh Admin.']);
        }

        // Cari user berdasarkan nama, ID, email, atau QR code
        $user = $this->findUserByIdentifier($request->identifier);

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Identitas Karyawan tidak terdaftar. Absensi ditolak!']);
        }

        // Cek absensi hari ini (Mencegah absen ganda)
        $absenHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->where('type', $request->type)
            ->exists();

        if ($absenHariIni) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen '.$request->type.' hari ini.']);
        }

        // Cek Jarak (Haversine Formula) - jika latitude/longitude kedai di-set
        $status = 'Tepat Waktu';
        if ($kedai->latitude && $kedai->longitude) {
            $distance = $this->calculateDistance($kedai->latitude, $kedai->longitude, $request->latitude, $request->longitude);
            if ($distance > $kedai->radius_meter) {
                return response()->json(['success' => false, 'message' => 'Anda berada di luar zona absen. Jarak Anda: '.round($distance).'m. Radius Maksimal: '.$kedai->radius_meter.'m']);
            }
        }

        // Hitung Keterlambatan berbasis Shift (jika tipe = Masuk)
        if ($request->type == 'Masuk' && ! str_contains($status, 'Luar Zona')) {
            $hariIni = Carbon::now()->locale('id')->isoFormat('dddd');
            $shift = Shift::where('hari', $hariIni)
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->first();

            if ($shift && ! $shift->is_libur) {
                $waktuMulai = Carbon::createFromFormat('H:i:s', $shift->jam_mulai);
                $sekarang = Carbon::now();
                if ($sekarang->gt($waktuMulai)) {
                    $terlambatMenit = $sekarang->diffInMinutes($waktuMulai);
                    $status = 'Terlambat '.$terlambatMenit.' Menit';
                }
            }
        }

        // Proses Foto (Base64 ke File)
        $photoData = $request->photo;
        [$type, $photoData] = explode(';', $photoData);
        [, $photoData] = explode(',', $photoData);
        $photoData = base64_decode($photoData);
        $fileName = 'absensi/'.$user->id.'_'.time().'.png';
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
            'message' => 'Berhasil Absen '.$request->type.'! Selamat bekerja, '.$user->name,
            'status' => $status,
        ]);
    }

    public function checkUser(Request $request)
    {
        $request->validate(['identifier' => 'required']);
        $user = $this->findUserByIdentifier($request->identifier);

        if ($user) {
            $absenMasuk = Absensi::where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->where('type', 'Masuk')
                ->first();

            $absenKeluar = Absensi::where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->where('type', 'Keluar')
                ->first();

            $suggestedType = 'Masuk';
            if ($absenMasuk && ! $absenKeluar) {
                $suggestedType = 'Keluar';
            } elseif ($absenMasuk && $absenKeluar) {
                $suggestedType = 'Selesai';
            }

            return response()->json([
                'success' => true,
                'name' => $user->name,
                'user_id' => $user->id,
                'role' => ucfirst($user->role),
                'position' => $user->position ?: ($user->role === 'admin' ? 'Administrator' : 'Staf Kedai'),
                'has_masuk' => (bool) $absenMasuk,
                'masuk_time' => $absenMasuk ? $absenMasuk->created_at->format('H:i') : null,
                'has_keluar' => (bool) $absenKeluar,
                'keluar_time' => $absenKeluar ? $absenKeluar->created_at->format('H:i') : null,
                'suggested_type' => $suggestedType,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Identitas Karyawan "'.htmlspecialchars($request->identifier).'" tidak ditemukan. Absensi ditolak!',
        ]);
    }

    public function findUserByIdentifier(?string $identifier): ?User
    {
        if (! $identifier) {
            return null;
        }

        $identifier = trim($identifier);
        if ($identifier === '' || $identifier === '0' || preg_match('/^0+$/', $identifier)) {
            return null;
        }

        // 1. Cek apakah input ID numerik (contoh: 5, 05, 005, #5)
        $cleanId = ltrim(str_replace('#', '', $identifier), '0');
        if ($cleanId !== '' && ctype_digit($cleanId) && (int) $cleanId > 0) {
            $user = User::find((int) $cleanId);
            if ($user) {
                return $user;
            }
        }

        // 2. Cek apakah cocok dengan nama lengkap (case-insensitive)
        $userByName = User::whereRaw('LOWER(name) = ?', [strtolower($identifier)])->first();
        if ($userByName) {
            return $userByName;
        }

        // 3. Cek apakah cocok dengan email atau QR code
        $userByEmailOrQr = User::where('email', $identifier)
            ->orWhere('qr_code', $identifier)
            ->first();
        if ($userByEmailOrQr) {
            return $userByEmailOrQr;
        }

        return null;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
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
