<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kasir\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('kasir.dashboard');

// Belanja Harian / Pengeluaran
Route::get('/pengeluaran', [\App\Http\Controllers\Kasir\PengeluaranController::class, 'index'])->name('kasir.pengeluaran');
Route::post('/pengeluaran', [\App\Http\Controllers\Kasir\PengeluaranController::class, 'store'])->name('kasir.pengeluaran.store');

// Laporan
Route::prefix('laporan')->name('kasir.laporan.')->group(function () {
    Route::get('/penjualan', [\App\Http\Controllers\Kasir\LaporanController::class, 'penjualan'])->name('penjualan');
    Route::get('/pengeluaran', [\App\Http\Controllers\Kasir\LaporanController::class, 'pengeluaran'])->name('pengeluaran');
    Route::get('/shift', [\App\Http\Controllers\Kasir\LaporanController::class, 'shift'])->name('shift');
});

// Lainnya
Route::post('/cetak-struk', [\App\Http\Controllers\Kasir\TransaksiController::class, 'cetakStruk'])->name('kasir.cetak_struk');
Route::get('/cetak-ulang/{id}', [\App\Http\Controllers\Kasir\TransaksiController::class, 'cetakUlang'])->name('kasir.cetak_ulang');
Route::get('/riwayat-invoice', [\App\Http\Controllers\Kasir\TransaksiController::class, 'invoice'])->name('kasir.invoice');
Route::get('/absensi', [\App\Http\Controllers\Kasir\AbsensiController::class, 'index'])->name('kasir.absensi');
Route::post('/absensi', [\App\Http\Controllers\Kasir\AbsensiController::class, 'store'])->name('kasir.absensi.store');
Route::get('/jadwal-shift', [\App\Http\Controllers\Kasir\ShiftController::class, 'index'])->name('kasir.shift_jadwal');
Route::get('/status-meja', [\App\Http\Controllers\Kasir\MejaController::class, 'index'])->name('kasir.meja');
Route::get('/status-meja/{meja}/qr', [\App\Http\Controllers\Kasir\MejaController::class, 'downloadQr'])->name('kasir.meja.qr');
Route::post('/cek-voucher', [\App\Http\Controllers\Kasir\VoucherController::class, 'cek'])->name('kasir.cek_voucher');
Route::get('/tutup-kasir', [\App\Http\Controllers\Kasir\SesiController::class, 'index'])->name('kasir.tutup');
Route::post('/sesi/buka', [\App\Http\Controllers\Kasir\SesiController::class, 'buka'])->name('kasir.sesi.buka');
Route::post('/sesi/tutup', [\App\Http\Controllers\Kasir\SesiController::class, 'tutup'])->name('kasir.sesi.tutup');
