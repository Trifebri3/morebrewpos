<?php

namespace App\Exports;

use App\Models\Produk;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProdukExport implements FromCollection, WithHeadings
{
    protected $kedai_id;

    public function __construct($kedai_id)
    {
        $this->kedai_id = $kedai_id;
    }

    public function collection(): Enumerable
    {
        return Produk::where('kedai_id', $this->kedai_id)->select('sku', 'name', 'category', 'price', 'stock', 'is_active')->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Nama Produk',
            'Kategori',
            'Harga',
            'Stok',
            'Status Aktif (1/0)',
        ];
    }
}
