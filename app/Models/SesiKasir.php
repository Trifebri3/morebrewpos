<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiKasir extends Model
{
    protected $fillable = [
        'user_id', 'waktu_buka', 'waktu_tutup', 'modal_awal',
        'total_pendapatan', 'uang_fisik', 'selisih', 'status', 'catatan'
    ];
    
    protected $casts = [
        'waktu_buka' => 'datetime',
        'waktu_tutup' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
