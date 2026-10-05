<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AbsensiExport;
use App\Exports\KasExport;
use App\Exports\PengeluaranExport;
use App\Exports\PenjualanExport;
use App\Exports\ProdukLaporanExport;
use App\Exports\ShiftExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Pengeluaran;
use App\Models\Produk;
use App\Models\SesiKasir;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\Admin\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function penjualan(DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Laporan Penjualan';

        $type = $request->query('type', 'harian');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Transaksi::query();

        if ($type === 'harian') {
            $query->whereDate('created_at', today());
            $data['subtitle'] = 'Hari Ini ('.today()->format('d M Y').')';
        } elseif ($type === 'bulanan') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
            $data['subtitle'] = 'Bulan '.now()->format('F Y');
        } elseif ($type === 'pertanggal') {
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    $startDate.' 00:00:00',
                    $endDate.' 23:59:59',
                ]);
                $data['subtitle'] = 'Periode: '.$startDate.' s/d '.$endDate;
            } else {
                $query->whereDate('created_at', today());
                $data['subtitle'] = 'Pilih Rentang Tanggal';
            }
        } elseif ($type === 'lengkap') {
            $data['subtitle'] = 'Rekapan Keseluruhan';
        }

        $transaksis = $query->latest()->get();

        $itemsSold = [];
        $totalPenjualan = 0;
        $totalDiskon = 0;

        foreach ($transaksis as $t) {
            $totalPenjualan += $t->total;
            $totalDiskon += ($t->discount_amount ?? 0);
            $items = is_string($t->items) ? json_decode($t->items, true) : $t->items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $name = $item['name'] ?? 'Unknown';
                    $qty = $item['qty'] ?? 1;
                    $itemsSold[$name] = ($itemsSold[$name] ?? 0) + $qty;
                }
            }
        }
        arsort($itemsSold);

        return view('admin.laporan.penjualan', compact('data', 'transaksis', 'type', 'itemsSold', 'totalPenjualan', 'totalDiskon', 'startDate', 'endDate'));
    }

    public function exportPenjualan(Request $request)
    {
        return Excel::download(
            new PenjualanExport($request->type ?? 'harian', $request->start_date, $request->end_date),
            'laporan_penjualan_'.date('YmdHis').'.xlsx'
        );
    }

    public function pengeluaran(DashboardService $dashboardService, Request $request)
    {
        $dashboardData = $dashboardService->getDashboardData();
        $dashboardData['title'] = 'Laporan Pengeluaran';

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Pengeluaran::query();

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $allPengeluaran = (clone $query)->orderBy('tanggal', 'desc')->get();

        // Group by date for daily report
        $laporanHarian = (clone $query)->selectRaw('DATE(tanggal) as tanggal_harian, SUM(nominal) as total_pengeluaran, COUNT(*) as jumlah_transaksi')
            ->where('status', 'approved')
            ->groupBy('tanggal_harian')
            ->orderBy('tanggal_harian', 'desc')
            ->get();

        $totalPengeluaran = $allPengeluaran->where('status', 'approved')->sum('nominal');

        return view('admin.laporan.pengeluaran', array_merge($dashboardData, compact('laporanHarian', 'allPengeluaran', 'totalPengeluaran', 'startDate', 'endDate')));
    }

    public function exportPengeluaran(Request $request)
    {
        return Excel::download(
            new PengeluaranExport($request->start_date, $request->end_date),
            'laporan_pengeluaran_'.date('YmdHis').'.xlsx'
        );
    }

    public function produk(DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Laporan Produk & Menu';

        $kedaiId = auth()->user()->kedai_id;
        $query = Produk::query();
        if ($kedaiId) {
            $query->where('kedai_id', $kedaiId);
        }
        $produks = $query->orderBy('name')->get();

        $transaksis = Transaksi::all();
        $salesCount = [];
        $salesRevenue = [];

        foreach ($transaksis as $t) {
            $items = is_string($t->items) ? json_decode($t->items, true) : $t->items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $name = $item['name'] ?? null;
                    $qty = $item['qty'] ?? 1;
                    $price = $item['price'] ?? 0;
                    if ($name) {
                        $salesCount[$name] = ($salesCount[$name] ?? 0) + $qty;
                        $salesRevenue[$name] = ($salesRevenue[$name] ?? 0) + ($qty * $price);
                    }
                }
            }
        }

        foreach ($produks as $p) {
            $p->terjual = $salesCount[$p->name] ?? 0;
            $p->omzet = $salesRevenue[$p->name] ?? 0;
        }

        $totalProduk = $produks->count();
        $totalTerjual = array_sum($salesCount);
        $totalOmzet = array_sum($salesRevenue);

        return view('admin.laporan.produk', compact('data', 'produks', 'totalProduk', 'totalTerjual', 'totalOmzet'));
    }

    public function exportProduk()
    {
        return Excel::download(
            new ProdukLaporanExport(auth()->user()->kedai_id),
            'laporan_produk_'.date('YmdHis').'.xlsx'
        );
    }

    public function kas(DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Laporan Kas & Arus Kas';

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $transaksiQuery = Transaksi::where(function ($q) {
            $q->whereNull('payment_method')
                ->orWhere('payment_method', 'tunai')
                ->orWhere('payment_method', 'cash');
        });

        $pengeluaranQuery = Pengeluaran::where('status', 'approved');

        if ($startDate && $endDate) {
            $transaksiQuery->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            $pengeluaranQuery->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $transaksis = $transaksiQuery->get();
        $pengeluarans = $pengeluaranQuery->get();
        $sesis = SesiKasir::all();

        $allDates = collect();
        foreach ($transaksis as $t) {
            $allDates->push($t->created_at->format('Y-m-d'));
        }
        foreach ($pengeluarans as $p) {
            $allDates->push(Carbon::parse($p->tanggal)->format('Y-m-d'));
        }
        if ($allDates->isEmpty()) {
            $allDates->push(now()->format('Y-m-d'));
        }

        $dates = $allDates->unique()->sortDesc();
        $kasRows = collect();

        foreach ($dates as $date) {
            $inflow = $transaksis->filter(fn ($t) => $t->created_at->format('Y-m-d') === $date)->sum('total');
            $outflow = $pengeluarans->filter(fn ($p) => Carbon::parse($p->tanggal)->format('Y-m-d') === $date)->sum('nominal');
            $modal = $sesis->filter(fn ($s) => $s->waktu_buka && $s->waktu_buka->format('Y-m-d') === $date)->sum('modal_awal');
            $net = ($inflow + $modal) - $outflow;

            $kasRows->push((object) [
                'tanggal' => $date,
                'pemasukan' => $inflow,
                'modal' => $modal,
                'pengeluaran' => $outflow,
                'saldo_bersih' => $net,
                'status' => $net >= 0 ? 'Surplus' : 'Defisit',
            ]);
        }

        $totalKasMasuk = $kasRows->sum('pemasukan');
        $totalKasKeluar = $kasRows->sum('pengeluaran');
        $totalSaldoBersih = $totalKasMasuk - $totalKasKeluar;

        return view('admin.laporan.kas', compact('data', 'kasRows', 'totalKasMasuk', 'totalKasKeluar', 'totalSaldoBersih', 'startDate', 'endDate'));
    }

    public function exportKas(Request $request)
    {
        return Excel::download(
            new KasExport($request->start_date, $request->end_date),
            'laporan_kas_'.date('YmdHis').'.xlsx'
        );
    }

    public function shift(DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Laporan Shift Kasir';

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = SesiKasir::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('waktu_buka', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        }

        $sesis = $query->latest('waktu_buka')->get();

        $totalSesi = $sesis->count();
        $totalPendapatan = $sesis->sum('total_pendapatan');
        $totalSelisih = $sesis->sum('selisih');

        return view('admin.laporan.shift', compact('data', 'sesis', 'totalSesi', 'totalPendapatan', 'totalSelisih', 'startDate', 'endDate'));
    }

    public function exportShift(Request $request)
    {
        return Excel::download(
            new ShiftExport($request->start_date, $request->end_date),
            'laporan_shift_'.date('YmdHis').'.xlsx'
        );
    }

    public function staff(DashboardService $dashboardService, Request $request)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Laporan & Rekap Staff';

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $userId = $request->query('user_id');
        $status = $request->query('status');

        $query = Absensi::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        } else {
            // Default 30 hari terakhir
            $query->where('created_at', '>=', now()->subDays(30));
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            if ($status === 'tepat_waktu') {
                $query->where('status', 'like', '%Tepat Waktu%');
            } elseif ($status === 'terlambat') {
                $query->where('status', 'like', '%Terlambat%');
            } elseif ($status === 'luar_zona') {
                $query->where('status', 'like', '%Luar Zona%');
            }
        }

        $absensis = $query->latest('created_at')->get();
        $users = User::whereIn('role', ['admin', 'kasir'])->get();

        $totalAbsen = $absensis->count();
        $totalTepatWaktu = $absensis->filter(fn ($a) => str_contains($a->status, 'Tepat Waktu'))->count();
        $totalTerlambat = $absensis->filter(fn ($a) => str_contains($a->status, 'Terlambat'))->count();
        $totalDenganFoto = $absensis->filter(fn ($a) => ! empty($a->photo_path))->count();

        return view('admin.laporan.staff', compact(
            'data', 'absensis', 'users', 'totalAbsen',
            'totalTepatWaktu', 'totalTerlambat', 'totalDenganFoto',
            'startDate', 'endDate', 'userId', 'status'
        ));
    }

    public function exportStaff(Request $request)
    {
        return Excel::download(
            new AbsensiExport($request->start_date, $request->end_date, $request->user_id, $request->status),
            'laporan_staff_absensi_'.date('YmdHis').'.xlsx'
        );
    }
}
