<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Services\Kasir\DashboardService;

class AbsensiController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();
        
        $absensiHariIni = Absensi::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->get();
            
        $sudahMasuk = $absensiHariIni->where('type', 'Masuk')->isNotEmpty();
        $sudahKeluar = $absensiHariIni->where('type', 'Keluar')->isNotEmpty();
        
        $riwayat = Absensi::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $kedai = \App\Models\Kedai::first();
        
        $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
        $shiftsHariIni = \App\Models\Shift::with(['users'])->where('hari', $hariIni)->get();
        $semuaAbsensi = Absensi::whereDate('created_at', \Carbon\Carbon::today())->get();
        
        $recap = [];
        foreach ($shiftsHariIni as $shift) {
            $shiftRecap = [
                'name' => $shift->nama_shift,
                'jam' => $shift->jam_mulai . ' - ' . $shift->jam_selesai,
                'total_karyawan' => $shift->users->count(),
                'hadir' => 0,
                'terlambat' => 0
            ];
            
            foreach ($shift->users as $u) {
                $absenUser = $semuaAbsensi->where('user_id', $u->id)->where('type', 'Masuk')->first();
                if ($absenUser) {
                    $shiftRecap['hadir']++;
                    if (str_contains($absenUser->status, 'Terlambat') || str_contains($absenUser->status, 'Luar Zona')) {
                        $shiftRecap['terlambat']++;
                    }
                }
            }
            $recap[] = $shiftRecap;
        }

        return view('kasir.absensi.index', array_merge($data, [
            'sudahMasuk' => $sudahMasuk,
            'sudahKeluar' => $sudahKeluar,
            'riwayat' => $riwayat,
            'kedai' => $kedai,
            'recap' => $recap
        ]));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta / 2) * sin($latDelta / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:Masuk,Keluar',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $kedai = \App\Models\Kedai::first();
        if ($kedai && $kedai->latitude && $kedai->longitude) {
            if (!$request->latitude || !$request->longitude) {
                return redirect()->back()->with('error', 'Lokasi (GPS) wajib diaktifkan untuk melakukan absen.');
            }
            $distance = $this->calculateDistance($kedai->latitude, $kedai->longitude, $request->latitude, $request->longitude);
            if ($distance > $kedai->radius_meter) {
                return redirect()->back()->with('error', 'Anda berada di luar zona absen. Jarak Anda: ' . round($distance) . 'm (Maks: ' . $kedai->radius_meter . 'm).');
            }
        }

        // Cek jika sudah absensi tipe tersebut hari ini
        $sudah = Absensi::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->where('type', $request->type)
            ->exists();
            
        if ($sudah) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi ' . $request->type . ' hari ini.');
        }

        Absensi::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'valid',
        ]);

        return redirect()->route('kasir.absensi')->with('success', 'Berhasil melakukan Clock In/Out (' . $request->type . ').');
    }
}
