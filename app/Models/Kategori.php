<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kedai_id', 'name', 'prefix'])]
class Kategori extends Model
{
    public function kedai()
    {
        return $this->belongsTo(Kedai::class);
    }
}
