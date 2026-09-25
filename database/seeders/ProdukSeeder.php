<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\Kedai;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kedais = Kedai::all();
        if ($kedais->isEmpty()) {
            echo "Belum ada data Kedai. Harap buat Kedai terlebih dahulu.\n";
            return;
        }

        $menus = [
            ['name' => 'MANGGO COFFEE', 'price' => 29000],
            ['name' => 'BLUEBERRY COFFEE', 'price' => 29000],
            ['name' => 'GUAVA COFFEE', 'price' => 29000],
            ['name' => 'SIRSAK COFFEE', 'price' => 28000],
            ['name' => 'CHOCOLATE', 'price' => 35000],
            ['name' => 'MATCHA', 'price' => 30000],
            ['name' => 'AMERICANO', 'price' => 25000],
            ['name' => 'CAPPUCINO', 'price' => 28000],
            ['name' => 'LATTE', 'price' => 28000],
            ['name' => 'MAGIC', 'price' => 28000],
            ['name' => 'ESPRESSO', 'price' => 21000],
            ['name' => 'MANGO YAKULT', 'price' => 26000],
            ['name' => 'GUAVA YAKULT', 'price' => 26000],
            ['name' => 'SIRSAK YAKULT', 'price' => 26000],
            ['name' => 'BLUEBERRY YAKULT', 'price' => 26000],
            ['name' => 'MANUAL BREW', 'price' => 35000],
            ['name' => 'ESKOPI SUSU', 'price' => 22000],
        ];

        foreach ($kedais as $kedai) {
            foreach ($menus as $menu) {
                Produk::create([
                    'kedai_id' => $kedai->id,
                    'name' => $menu['name'],
                    'category' => 'Minuman',
                    'price' => $menu['price'],
                    'stock' => 100, // Default stok
                    'is_active' => true,
                ]);
            }
        }
    }
}
