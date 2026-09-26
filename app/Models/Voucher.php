<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    /** @use HasFactory<\Database\Factories\VoucherFactory> */
    use HasFactory;

    protected $fillable = [
        'kedai_id',
        'kode',
        'nama',
        'tipe_diskon',
        'nilai_diskon',
        'minimal_belanja',
        'tanggal_mulai',
        'tanggal_selesai',
        'hari_berlaku',
        'jam_mulai',
        'jam_selesai',
        'kuota',
        'terpakai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'hari_berlaku' => 'array',
        'status' => 'boolean',
    ];

    public function kedai()
    {
        return $this->belongsTo(Kedai::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
