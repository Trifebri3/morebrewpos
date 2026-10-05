<?php

namespace App\Exports;

use App\Models\Pengeluaran;
use App\Models\SesiKasir;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KasExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $startDate;

    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection(): Enumerable
    {
        // Group by date
        $allDates = collect();

        $transaksiQuery = Transaksi::where(function ($q) {
            $q->whereNull('payment_method')
                ->orWhere('payment_method', 'tunai')
                ->orWhere('payment_method', 'cash');
        });

        $pengeluaranQuery = Pengeluaran::where('status', 'approved');

        if ($this->startDate && $this->endDate) {
            $transaksiQuery->whereBetween('created_at', [$this->startDate.' 00:00:00', $this->endDate.' 23:59:59']);
            $pengeluaranQuery->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        }

        $transaksis = $transaksiQuery->get();
        $pengeluarans = $pengeluaranQuery->get();
        $sesis = SesiKasir::all();

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
        $rows = collect();

        foreach ($dates as $date) {
            $inflow = $transaksis->filter(fn ($t) => $t->created_at->format('Y-m-d') === $date)->sum('total');
            $outflow = $pengeluarans->filter(fn ($p) => Carbon::parse($p->tanggal)->format('Y-m-d') === $date)->sum('nominal');
            $modal = $sesis->filter(fn ($s) => $s->waktu_buka && $s->waktu_buka->format('Y-m-d') === $date)->sum('modal_awal');
            $net = ($inflow + $modal) - $outflow;

            $rows->push((object) [
                'tanggal' => $date,
                'pemasukan' => $inflow,
                'modal' => $modal,
                'pengeluaran' => $outflow,
                'saldo_bersih' => $net,
                'status' => $net >= 0 ? 'Surplus' : 'Defisit',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Penjualan Tunai (Rp)',
            'Modal Awal Kasir (Rp)',
            'Pengeluaran Tunai (Rp)',
            'Arus Kas Bersih (Rp)',
            'Keterangan Arus Kas',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal,
            $row->pemasukan,
            $row->modal,
            $row->pengeluaran,
            $row->saldo_bersih,
            $row->status,
        ];
    }
}
