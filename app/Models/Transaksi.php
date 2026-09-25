<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_name', 'order_type', 'items',
        'subtotal', 'discount_amount', 'tax', 'total',
        'payment_method', 'amount_paid'
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
