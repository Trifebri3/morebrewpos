<?php

namespace App\Exports;

use App\Models\Pengeluaran;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengeluaranExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
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
        $query = Pengeluaran::with(['user', 'approver']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        }

        return $query->orderBy('tanggal', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal',
            'Nama Pengeluaran / Item',
            'Nominal (Rp)',
            'Keterangan',
            'Dicatat Oleh',
            'Status Approval',
            'Disetujui Oleh',
        ];
    }

    public function map($p): array
    {
        return [
            $p->id,
            $p->tanggal,
            $p->nama_item,
            $p->nominal,
            $p->keterangan ?: '-',
            $p->user ? $p->user->name : '-',
            strtoupper($p->status ?? 'APPROVED'),
            $p->approver ? $p->approver->name : '-',
        ];
    }
}
