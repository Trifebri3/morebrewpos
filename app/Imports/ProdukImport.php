<?php

namespace App\Imports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProdukImport implements ToModel, WithHeadingRow
{
    protected $kedai_id;

    public function __construct($kedai_id)
    {
        $this->kedai_id = $kedai_id;
    }

    public function model(array $row)
    {
        return new Produk([
            'kedai_id'  => $this->kedai_id,
            'sku'       => $row['sku'],
            'name'      => $row['nama_produk'],
            'category'  => $row['kategori'],
            'price'     => $row['harga'],
            'stock'     => $row['stok'],
            'is_active' => $row['status_aktif_10'] ?? 1,
        ]);
    }
}
