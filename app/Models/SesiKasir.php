<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiKasir extends Model
{
    protected $fillable = [
        'user_id', 'session_number', 'previous_session_id', 'waktu_buka', 'waktu_tutup', 'modal_awal',
        'total_pendapatan', 'total_cash_sales', 'total_non_cash_sales', 'cash_in', 'cash_out', 'cash_expense',
        'expected_balance', 'uang_fisik', 'selisih', 'status', 'catatan', 'created_at'
    ];
    
    protected $casts = [
        'waktu_buka' => 'datetime',
        'waktu_tutup' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'sesi_kasir_id');
    }
}
