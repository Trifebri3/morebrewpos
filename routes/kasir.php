<?php

use App\Http\Controllers\Kasir\AbsensiController;
use App\Http\Controllers\Kasir\DashboardController;
use App\Http\Controllers\Kasir\LaporanController;
use App\Http\Controllers\Kasir\MejaController;
use App\Http\Controllers\Kasir\PengeluaranController;
use App\Http\Controllers\Kasir\SesiController;
use App\Http\Controllers\Kasir\ShiftController;
use App\Http\Controllers\Kasir\TransaksiController;
use App\Http\Controllers\Kasir\VoucherController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('kasir.dashboard');

// Belanja Harian / Pengeluaran
Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('kasir.pengeluaran');
Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('kasir.pengeluaran.store');

// Laporan
Route::prefix('laporan')->name('kasir.laporan.')->group(function () {
    Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
    Route::get('/penjualan/export', [LaporanController::class, 'exportPenjualan'])->name('penjualan.export');
    Route::get('/pengeluaran', [LaporanController::class, 'pengeluaran'])->name('pengeluaran');
    Route::get('/pengeluaran/export', [LaporanController::class, 'exportPengeluaran'])->name('pengeluaran.export');
    Route::get('/shift', [LaporanController::class, 'shift'])->name('shift');
    Route::get('/shift/export', [LaporanController::class, 'exportShift'])->name('shift.export');
});

// Lainnya
Route::post('/cetak-struk', [TransaksiController::class, 'cetakStruk'])->name('kasir.cetak_struk');
Route::post('/simpan-open-tab', [TransaksiController::class, 'simpanOpenTab'])->name('kasir.simpan_open_tab');
Route::get('/cetak-ulang/{id}', [TransaksiController::class, 'cetakUlang'])->name('kasir.cetak_ulang');
Route::get('/riwayat-invoice', [TransaksiController::class, 'invoice'])->name('kasir.invoice');
Route::get('/absensi', [AbsensiController::class, 'index'])->name('kasir.absensi');
Route::post('/absensi', [AbsensiController::class, 'store'])->name('kasir.absensi.store');
Route::get('/jadwal-shift', [ShiftController::class, 'index'])->name('kasir.shift_jadwal');
Route::get('/status-meja', [MejaController::class, 'index'])->name('kasir.meja');
Route::get('/status-meja/{meja}/qr', [MejaController::class, 'downloadQr'])->name('kasir.meja.qr');
Route::post('/cek-voucher', [VoucherController::class, 'cek'])->name('kasir.cek_voucher');
Route::get('/tutup-kasir', [SesiController::class, 'index'])->name('kasir.tutup');
Route::post('/sesi/buka', [SesiController::class, 'buka'])->name('kasir.sesi.buka');
Route::post('/sesi/tutup', [SesiController::class, 'tutup'])->name('kasir.sesi.tutup');
