<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AbsensiExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kedai;
use App\Models\Shift;
use App\Models\User;
use App\Services\Admin\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiController extends Controller
{
    public function index(DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Absensi Karyawan & Pengaturan';

        $range = $request->query('range', 'hari_ini');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $userId = $request->query('user_id');
        $statusFilter = $request->query('status');

        $query = Absensi::with('user');

        if ($range === 'hari_ini') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($range === '7_hari') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($range === 'bulan_ini') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
        } elseif ($range === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($statusFilter) {
            if ($statusFilter === 'tepat_waktu') {
                $query->where('status', 'like', '%Tepat Waktu%');
            } elseif ($statusFilter === 'terlambat') {
                $query->where('status', 'like', '%Terlambat%');
            } elseif ($statusFilter === 'luar_zona') {
                $query->where('status', 'like', '%Luar Zona%');
            }
        }

        $absensis = $query->orderBy('created_at', 'desc')->get();
        $users = User::whereIn('role', ['admin', 'kasir'])->orderBy('name')->get();

        $kedai = Kedai::first();
        $hariIni = Carbon::now()->locale('id')->isoFormat('dddd');

        // Dapatkan shift hari ini
        $shiftsHariIni = Shift::with(['users'])->where('hari', $hariIni)->get();

        $absensiHariIni = Absensi::whereDate('created_at', Carbon::today())->get();

        $recap = [
            'total_karyawan' => 0,
            'hadir' => 0,
            'terlambat' => 0,
            'belum_absen' => 0,
            'shifts' => [],
        ];

        foreach ($shiftsHariIni as $shift) {
            $shiftRecap = [
                'name' => $shift->nama_shift,
                'jam' => $shift->jam_mulai.' - '.$shift->jam_selesai,
                'total_karyawan' => $shift->users->count(),
                'hadir' => 0,
                'terlambat' => 0,
            ];

            $recap['total_karyawan'] += $shift->users->count();

            foreach ($shift->users as $u) {
                $absenUser = $absensiHariIni->where('user_id', $u->id)->where('type', 'Masuk')->first();
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
        $recap['belum_absen'] = max(0, $recap['total_karyawan'] - $recap['hadir']);

        // Summary statistics for the filtered records
        $filterStats = [
            'total' => $absensis->count(),
            'tepat_waktu' => $absensis->filter(fn ($a) => str_contains($a->status, 'Tepat Waktu'))->count(),
            'terlambat' => $absensis->filter(fn ($a) => str_contains($a->status, 'Terlambat'))->count(),
            'dengan_foto' => $absensis->filter(fn ($a) => ! empty($a->photo_path))->count(),
        ];

        return view('admin.staff.absensi.index', compact(
            'data', 'absensis', 'kedai', 'recap', 'users',
            'range', 'startDate', 'endDate', 'userId', 'statusFilter', 'filterStats'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($request->range === 'hari_ini') {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = Carbon::today()->format('Y-m-d');
        } elseif ($request->range === '7_hari') {
            $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        } elseif ($request->range === 'bulan_ini') {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        return Excel::download(
            new AbsensiExport($startDate, $endDate, $request->user_id, $request->status),
            'rekapan_absensi_'.date('YmdHis').'.xlsx'
        );
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $user = User::where('qr_code', $request->qr_code)->first();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'QR Code tidak valid atau karyawan tidak ditemukan!'], 404);
        }

        // Cek absensi hari ini
        $absenHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->get();

        $tipe = 'Masuk';
        if ($absenHariIni->count() == 1) {
            $tipe = 'Keluar';
        } elseif ($absenHariIni->count() >= 2) {
            return response()->json(['status' => 'error', 'message' => 'Karyawan '.$user->name.' sudah absen Masuk dan Keluar hari ini!'], 400);
        }

        // Hitung Keterlambatan (Opsional: berbasis Shift)
        $status = 'Tepat Waktu';
        $terlambatMenit = 0;

        if ($tipe == 'Masuk') {
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

        $absen = Absensi::create([
            'user_id' => $user->id,
            'type' => $tipe,
            'status' => $status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absen '.$tipe.' berhasil untuk '.$user->name,
            'data' => [
                'name' => $user->name,
                'type' => $tipe,
                'status' => $status,
                'time' => $absen->created_at->format('H:i:s'),
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $kedai = Kedai::first();
        if ($kedai) {
            $kedai->update([
                'is_qr_absen_enabled' => $request->has('is_qr_absen_enabled'),
                'is_link_absen_enabled' => $request->has('is_link_absen_enabled'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meter' => $request->radius_meter ?? 50,
            ]);
        }

        return back()->with('success', 'Pengaturan tipe absensi berhasil disimpan.');
    }
}
