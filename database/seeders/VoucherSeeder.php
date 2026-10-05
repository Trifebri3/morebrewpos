<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;
use App\Models\Kedai;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kedai = Kedai::first();
        $kedaiId = $kedai ? $kedai->id : 1;

        $vouchers = [
            [
                'kedai_id' => $kedaiId,
                'kode' => 'MOREBREW10',
                'nama' => 'Diskon 10% Spesial',
                'tipe_diskon' => 'persen',
                'nilai_diskon' => 10,
                'minimal_belanja' => 25000,
                'kuota' => 500,
                'status' => true,
            ],
            [
                'kedai_id' => $kedaiId,
                'kode' => 'HEMAT5K',
                'nama' => 'Potongan Hemat Rp 5.000',
                'tipe_diskon' => 'nominal',
                'nilai_diskon' => 5000,
                'minimal_belanja' => 30000,
                'kuota' => 300,
                'status' => true,
            ],
            [
                'kedai_id' => $kedaiId,
                'kode' => 'DISKON15',
                'nama' => 'Promo Diskon 15%',
                'tipe_diskon' => 'persen',
                'nilai_diskon' => 15,
                'minimal_belanja' => 50000,
                'kuota' => 200,
                'status' => true,
            ],
            [
                'kedai_id' => $kedaiId,
                'kode' => 'KOPISANTAI',
                'nama' => 'Potongan Ngopi Rp 10.000',
                'tipe_diskon' => 'nominal',
                'nilai_diskon' => 10000,
                'minimal_belanja' => 60000,
                'kuota' => 150,
                'status' => true,
            ],
        ];

        foreach ($vouchers as $v) {
            Voucher::updateOrCreate(
                ['kode' => $v['kode']],
                $v
            );
        }
    }
}
