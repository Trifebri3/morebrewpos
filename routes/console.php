<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\Produk;

// Tambahkan stok harian setiap hari jam 00:00 menjadi 100
Schedule::call(function () {
    Produk::query()->update(['stock' => 100]);
})->daily();
