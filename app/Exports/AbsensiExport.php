<?php

namespace App\Exports;

use App\Models\Absensi;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $startDate;

    protected $endDate;

    protected $userId;

    protected $status;

    public function __construct($startDate = null, $endDate = null, $userId = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userId = $userId;
        $this->status = $status;
    }

    public function collection(): Enumerable
    {
        $query = Absensi::with('user');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->status) {
            if ($this->status === 'tepat_waktu') {
                $query->where('status', 'like', '%Tepat Waktu%');
            } elseif ($this->status === 'terlambat') {
                $query->where('status', 'like', '%Terlambat%');
            } elseif ($this->status === 'luar_zona') {
                $query->where('status', 'like', '%Luar Zona%');
            }
        }

        return $query->latest('created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal',
            'Waktu Absen',
            'Nama Karyawan',
            'Role / Jabatan',
            'Tipe Absen',
            'Status Kehadiran',
            'Latitude',
            'Longitude',
            'URL Foto Selfie / Bukti',
        ];
    }

    public function map($a): array
    {
        $photoUrl = '-';
        if ($a->photo_path) {
            $photoUrl = url(Storage::url($a->photo_path));
        }

        return [
            $a->id,
            $a->created_at ? $a->created_at->format('Y-m-d') : '-',
            $a->created_at ? $a->created_at->format('H:i:s') : '-',
            $a->user ? $a->user->name : 'Staff',
            $a->user ? ucfirst($a->user->role) : 'Kasir',
            $a->type,
            $a->status,
            $a->latitude ?: '-',
            $a->longitude ?: '-',
            $photoUrl,
        ];
    }
}
