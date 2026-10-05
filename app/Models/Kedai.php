<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kedai extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'is_active',
        'tax_percentage',
        'is_tax_enabled',
        'tax_name',
        'budget_harian',
        'receipt_header',
        'receipt_footer',
        'is_qr_absen_enabled',
        'is_link_absen_enabled',
        'latitude',
        'longitude',
        'radius_meter',
        'wifi_ssid',
        'wifi_password',
        'instagram',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_tax_enabled' => 'boolean',
        'is_qr_absen_enabled' => 'boolean',
        'is_link_absen_enabled' => 'boolean',
        'tax_percentage' => 'float',
        'budget_harian' => 'float',
        'radius_meter' => 'integer',
    ];
}
