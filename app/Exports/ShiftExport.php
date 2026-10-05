<?php

namespace App\Exports;

use App\Models\SesiKasir;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ShiftExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
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
        $query = SesiKasir::with('user');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('waktu_buka', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }

        return $query->latest('waktu_buka')->get();
    }

    public function headings(): array
    {
        return [
            'ID Sesi',
            'Nama Kasir',
            'Waktu Buka',
            'Waktu Tutup',
            'Modal Awal (Rp)',
            'Total Pendapatan (Rp)',
            'Uang Fisik Kasir (Rp)',
            'Selisih (Rp)',
            'Status Sesi',
            'Catatan',
        ];
    }

    public function map($s): array
    {
        return [
            'SESI-'.str_pad($s->id, 5, '0', STR_PAD_LEFT),
            $s->user ? $s->user->name : 'Kasir',
            $s->waktu_buka ? $s->waktu_buka->format('Y-m-d H:i:s') : '-',
            $s->waktu_tutup ? $s->waktu_tutup->format('Y-m-d H:i:s') : 'Masih Berjalan',
            $s->modal_awal ?? 0,
            $s->total_pendapatan ?? 0,
            $s->uang_fisik ?? 0,
            $s->selisih ?? 0,
            strtoupper($s->status ?? 'BUKA'),
            $s->catatan ?: '-',
        ];
    }
}
