<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kedai_id', 'sku', 'name', 'image', 'category', 'price', 'stock', 'is_active'])]
class Produk extends Model
{
    public function kedai()
    {
        return $this->belongsTo(Kedai::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($produk) {
            if (empty($produk->sku)) {
                $kat = \App\Models\Kategori::where('kedai_id', $produk->kedai_id)
                    ->where('name', $produk->category)->first();
                $prefix = $kat && $kat->prefix ? strtoupper($kat->prefix) : strtoupper(substr($produk->category, 0, 3));
                if(empty($prefix)) $prefix = 'OTH';

                $latestProduct = static::where('kedai_id', $produk->kedai_id)
                    ->where('sku', 'like', $prefix . '-%')
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$latestProduct || !preg_match('/-(\d+)$/', $latestProduct->sku, $matches)) {
                    $number = 1;
                } else {
                    $number = (int)$matches[1] + 1;
                }

                $produk->sku = $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
