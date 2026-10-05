<?php

namespace App\Exports;

use App\Models\Transaksi;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PenjualanExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $type;

    protected $startDate;

    protected $endDate;

    public function __construct($type = 'harian', $startDate = null, $endDate = null)
    {
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection(): Enumerable
    {
        $query = Transaksi::query();

        if ($this->type === 'harian') {
            $query->whereDate('created_at', today());
        } elseif ($this->type === 'bulanan') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        } elseif ($this->type === 'pertanggal' && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'No Invoice',
            'Tanggal & Waktu',
            'Nama Pelanggan',
            'Tipe Pesanan',
            'Metode Pembayaran',
            'Subtotal',
            'Diskon',
            'Pajak',
            'Total Belanja',
            'Status Refund',
            'Alasan Refund',
        ];
    }

    public function map($t): array
    {
        return [
            $t->id,
            'INV-'.$t->invoice_number,
            $t->created_at ? $t->created_at->format('Y-m-d H:i:s') : '-',
            $t->customer_name ?: 'Pelanggan Umum',
            $t->order_type ?: 'Dine In',
            strtoupper($t->payment_method ?: 'TUNAI'),
            $t->subtotal ?? 0,
            $t->discount_amount ?? 0,
            $t->tax ?? 0,
            $t->total ?? 0,
            $t->is_refunded ? 'Direfund' : 'Selesai',
            $t->refund_reason ?: '-',
        ];
    }
}
