<?php

use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\AktivitasController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\KedaiController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Http\Controllers\Admin\PlaceholderController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\StokController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// Semua rute fitur Admin diarahkan ke PlaceholderController sementara
Route::prefix('operasional')->name('admin.operasional.')->group(function () {
    Route::get('/produk/export', [ProdukController::class, 'export'])->name('produk.export');
    Route::post('/produk/import', [ProdukController::class, 'import'])->name('produk.import');
    Route::resource('/produk', ProdukController::class);
    Route::resource('/kategori', KategoriController::class);
    Route::get('/harga', [PlaceholderController::class, 'show'])->name('harga');
    Route::get('/stok', [StokController::class, 'index'])->name('stok');
    Route::post('/stok', [StokController::class, 'update'])->name('stok.update');
    Route::get('/supplier', [PlaceholderController::class, 'show'])->name('supplier');
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::put('/pengeluaran/budget', [PengeluaranController::class, 'updateBudget'])->name('pengeluaran.budget');
    Route::put('/pengeluaran/{pengeluaran}/approve', [PengeluaranController::class, 'approve'])->name('pengeluaran.approve');
    Route::put('/pengeluaran/{pengeluaran}/reject', [PengeluaranController::class, 'reject'])->name('pengeluaran.reject');
});

Route::prefix('penjualan')->name('admin.penjualan.')->group(function () {
    Route::get('/transaksi', [PenjualanController::class, 'transaksi'])->name('transaksi');
    Route::get('/invoice', [PenjualanController::class, 'invoice'])->name('invoice');
    Route::get('/voucher/{voucher}/export', [VoucherController::class, 'exportUsage'])->name('voucher.export');
    Route::resource('/voucher', VoucherController::class);
    Route::get('/refund', [PenjualanController::class, 'refund'])->name('refund');
    Route::post('/refund/{transaksi}', [PenjualanController::class, 'processRefund'])->name('refund.process');
    Route::get('/riwayat', [PenjualanController::class, 'riwayat'])->name('riwayat');
});

Route::prefix('kedai')->name('admin.kedai.')->group(function () {
    Route::resource('/meja', MejaController::class);
    Route::get('/meja/{meja}/qr', [MejaController::class, 'downloadQr'])->name('meja.qr');
    Route::get('/printer', fn () => redirect()->route('admin.kedai.pengaturan'))->name('printer');
    Route::get('/pengaturan', [KedaiController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/pengaturan', [KedaiController::class, 'updatePengaturan'])->name('pengaturan.update');
});

Route::prefix('staff')->name('admin.staff.')->group(function () {
    Route::resource('/karyawan', KaryawanController::class);
    Route::get('/karyawan/{karyawan}/qr', [KaryawanController::class, 'qr'])->name('karyawan.qr');
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift');
    Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');
    Route::delete('/shift/{shift}', [ShiftController::class, 'destroy'])->name('shift.destroy');
    Route::post('/shift/{shift}/assign', [ShiftController::class, 'assignUser'])->name('shift.assign');
    Route::delete('/shift/{shift}/remove/{user}', [ShiftController::class, 'removeUser'])->name('shift.remove');
    Route::get('/absensi/export', [AbsensiController::class, 'export'])->name('absensi.export');
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
    Route::post('/absensi/settings', [AbsensiController::class, 'updateSettings'])->name('absensi.settings.update');
    Route::post('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');
    Route::get('/aktivitas', [AktivitasController::class, 'index'])->name('aktivitas');
});

Route::prefix('laporan')->name('admin.laporan.')->group(function () {
    Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
    Route::get('/penjualan/export', [LaporanController::class, 'exportPenjualan'])->name('penjualan.export');
    Route::get('/produk', [LaporanController::class, 'produk'])->name('produk');
    Route::get('/produk/export', [LaporanController::class, 'exportProduk'])->name('produk.export');
    Route::get('/kas', [LaporanController::class, 'kas'])->name('kas');
    Route::get('/kas/export', [LaporanController::class, 'exportKas'])->name('kas.export');
    Route::get('/pengeluaran', [LaporanController::class, 'pengeluaran'])->name('pengeluaran');
    Route::get('/pengeluaran/export', [LaporanController::class, 'exportPengeluaran'])->name('pengeluaran.export');
    Route::get('/shift', [LaporanController::class, 'shift'])->name('shift');
    Route::get('/shift/export', [LaporanController::class, 'exportShift'])->name('shift.export');
    Route::get('/staff', [LaporanController::class, 'staff'])->name('staff');
    Route::get('/staff/export', [LaporanController::class, 'exportStaff'])->name('staff.export');
});
