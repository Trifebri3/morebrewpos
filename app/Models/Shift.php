<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['hari', 'nama', 'jam_mulai', 'jam_selesai', 'is_libur'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'shift_user');
    }
}
