<?php

namespace App\Exports;

use App\Models\Voucher;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VoucherUsageExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $voucher;

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    public function collection(): Enumerable
    {
        return $this->voucher->transaksis()->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'No Invoice',
            'Tanggal & Waktu',
            'Kode Voucher',
            'Nama Voucher',
            'Nama Pelanggan',
            'Total Belanja (Rp)',
            'Diskon Diterima (Rp)',
            'Metode Pembayaran',
        ];
    }

    public function map($t): array
    {
        return [
            $t->id,
            'INV-'.$t->invoice_number,
            $t->created_at ? $t->created_at->format('Y-m-d H:i:s') : '-',
            $this->voucher->kode,
            $this->voucher->nama,
            $t->customer_name ?: 'Pelanggan Umum',
            $t->total ?? 0,
            $t->discount_amount ?? 0,
            strtoupper($t->payment_method ?: 'TUNAI'),
        ];
    }
}
