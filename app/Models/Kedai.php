<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kedai extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'is_active', 'is_qr_absen_enabled', 'is_link_absen_enabled', 'latitude', 'longitude', 'radius_meter'];
}
