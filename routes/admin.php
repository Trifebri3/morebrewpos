<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlaceholderController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// Semua rute fitur Admin diarahkan ke PlaceholderController sementara
Route::prefix('operasional')->name('admin.operasional.')->group(function () {
    Route::get('/produk/export', [\App\Http\Controllers\Admin\ProdukController::class, 'export'])->name('produk.export');
    Route::post('/produk/import', [\App\Http\Controllers\Admin\ProdukController::class, 'import'])->name('produk.import');
    Route::resource('/produk', \App\Http\Controllers\Admin\ProdukController::class);
    Route::resource('/kategori', \App\Http\Controllers\Admin\KategoriController::class);
    Route::get('/harga', [PlaceholderController::class, 'show'])->name('harga');
    Route::get('/stok', [\App\Http\Controllers\Admin\StokController::class, 'index'])->name('stok');
    Route::post('/stok', [\App\Http\Controllers\Admin\StokController::class, 'update'])->name('stok.update');
    Route::get('/supplier', [PlaceholderController::class, 'show'])->name('supplier');
    Route::get('/pengeluaran', [\App\Http\Controllers\Admin\PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::put('/pengeluaran/budget', [\App\Http\Controllers\Admin\PengeluaranController::class, 'updateBudget'])->name('pengeluaran.budget');
    Route::put('/pengeluaran/{pengeluaran}/approve', [\App\Http\Controllers\Admin\PengeluaranController::class, 'approve'])->name('pengeluaran.approve');
    Route::put('/pengeluaran/{pengeluaran}/reject', [\App\Http\Controllers\Admin\PengeluaranController::class, 'reject'])->name('pengeluaran.reject');
});

Route::prefix('penjualan')->name('admin.penjualan.')->group(function () {
    Route::get('/transaksi', [PlaceholderController::class, 'show'])->name('transaksi');
    Route::get('/invoice', [PlaceholderController::class, 'show'])->name('invoice');
    Route::get('/voucher', [PlaceholderController::class, 'show'])->name('voucher');
    Route::get('/refund', [PlaceholderController::class, 'show'])->name('refund');
    Route::get('/riwayat', [PlaceholderController::class, 'show'])->name('riwayat');
});

Route::prefix('kedai')->name('admin.kedai.')->group(function () {
    Route::resource('/meja', \App\Http\Controllers\MejaController::class);
    Route::get('/meja/{meja}/qr', [\App\Http\Controllers\MejaController::class, 'downloadQr'])->name('meja.qr');
    Route::get('/printer', [PlaceholderController::class, 'show'])->name('printer');
    Route::get('/pengaturan', [\App\Http\Controllers\Admin\KedaiController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/pengaturan', [\App\Http\Controllers\Admin\KedaiController::class, 'updatePengaturan'])->name('pengaturan.update');
});

Route::prefix('staff')->name('admin.staff.')->group(function () {
    Route::resource('/karyawan', \App\Http\Controllers\Admin\KaryawanController::class);
    Route::get('/karyawan/{karyawan}/qr', [\App\Http\Controllers\Admin\KaryawanController::class, 'qr'])->name('karyawan.qr');
    Route::get('/shift', [PlaceholderController::class, 'show'])->name('shift');
    Route::get('/absensi', [PlaceholderController::class, 'show'])->name('absensi');
    Route::get('/aktivitas', [PlaceholderController::class, 'show'])->name('aktivitas');
});

Route::prefix('laporan')->name('admin.laporan.')->group(function () {
    Route::get('/penjualan', [PlaceholderController::class, 'show'])->name('penjualan');
    Route::get('/produk', [PlaceholderController::class, 'show'])->name('produk');
    Route::get('/kas', [PlaceholderController::class, 'show'])->name('kas');
    Route::get('/pengeluaran', [\App\Http\Controllers\Admin\LaporanController::class, 'pengeluaran'])->name('pengeluaran');
    Route::get('/shift', [PlaceholderController::class, 'show'])->name('shift');
    Route::get('/staff', [PlaceholderController::class, 'show'])->name('staff');
});
