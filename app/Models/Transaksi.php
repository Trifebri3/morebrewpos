<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_name', 'order_type', 'items',
        'subtotal', 'discount_amount', 'voucher_id', 'tax', 'total',
        'payment_method', 'amount_paid', 'is_refunded', 'refund_reason', 'refunded_at',
        'user_id', 'sesi_kasir_id', 'created_at'
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sesiKasir()
    {
        return $this->belongsTo(SesiKasir::class, 'sesi_kasir_id');
    }
}
