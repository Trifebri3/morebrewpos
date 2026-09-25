<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Kedai;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kedais = Kedai::all();
        foreach ($kedais as $kedai) {
            Kategori::firstOrCreate(['kedai_id' => $kedai->id, 'name' => 'Minuman'], ['prefix' => 'MIN']);
            Kategori::firstOrCreate(['kedai_id' => $kedai->id, 'name' => 'Makanan'], ['prefix' => 'MAK']);
            Kategori::firstOrCreate(['kedai_id' => $kedai->id, 'name' => 'Snack'], ['prefix' => 'SNA']);
            Kategori::firstOrCreate(['kedai_id' => $kedai->id, 'name' => 'Lainnya'], ['prefix' => 'OTH']);
        }
    }
}
