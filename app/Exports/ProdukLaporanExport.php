<?php

namespace App\Exports;

use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProdukLaporanExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $kedaiId;

    public function __construct($kedaiId = null)
    {
        $this->kedaiId = $kedaiId;
    }

    public function collection(): Enumerable
    {
        $query = Produk::query();
        if ($this->kedaiId) {
            $query->where('kedai_id', $this->kedaiId);
        }

        $produks = $query->orderBy('name')->get();

        // Count sales from transaksis
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

        return $produks;
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Nama Menu / Produk',
            'Kategori',
            'Harga Satuan (Rp)',
            'Sisa Stok',
            'Total Terjual (Qty)',
            'Estimasi Omzet (Rp)',
            'Status Aktif',
        ];
    }

    public function map($p): array
    {
        return [
            $p->sku ?: '-',
            $p->name,
            $p->category ?: 'Umum',
            $p->price ?? 0,
            $p->stock ?? 0,
            $p->terjual ?? 0,
            $p->omzet ?? 0,
            $p->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}
