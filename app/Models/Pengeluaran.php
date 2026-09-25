<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kedai_id', 'user_id', 'tanggal', 'nama_item', 'nominal', 'keterangan', 'bukti', 'status', 'approved_by'])]
class Pengeluaran extends Model
{
    public function kedai()
    {
        return $this->belongsTo(Kedai::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
