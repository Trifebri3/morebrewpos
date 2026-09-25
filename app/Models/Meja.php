<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    protected $fillable = ['kedai_id', 'name', 'qr_hash'];

    public function kedai()
    {
        return $this->belongsTo(Kedai::class);
    }
}
