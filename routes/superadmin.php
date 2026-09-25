<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\KedaiController;
use App\Http\Controllers\Superadmin\AkunController;
use App\Http\Controllers\Superadmin\LaporanController;
use App\Http\Controllers\Superadmin\MonitoringController;
use App\Http\Controllers\Superadmin\PengaturanController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');

// Manajemen Kedai
Route::get('/kedai/performa', [KedaiController::class, 'performa'])->name('superadmin.kedai.performa');
Route::resource('/kedai', KedaiController::class, ['as' => 'superadmin']);

// Manajemen Akun
Route::get('/akun/admin-kasir', [AkunController::class, 'index'])->name('superadmin.akun.index');
Route::get('/akun/roles', [AkunController::class, 'roles'])->name('superadmin.akun.roles');
Route::get('/akun/impersonate/{user}', [AkunController::class, 'impersonate'])->name('superadmin.akun.impersonate');
Route::resource('/akun', AkunController::class, ['as' => 'superadmin', 'parameters' => ['akun' => 'user']])->except(['index', 'show']);

// Laporan & Keuangan
Route::get('/laporan/transaksi', [LaporanController::class, 'transaksi'])->name('superadmin.laporan.transaksi');
Route::get('/laporan/omzet', [LaporanController::class, 'omzet'])->name('superadmin.laporan.omzet');
Route::get('/laporan/lengkap', [LaporanController::class, 'lengkap'])->name('superadmin.laporan.lengkap');

// Monitoring
Route::get('/monitoring/stok', [MonitoringController::class, 'stok'])->name('superadmin.monitoring.stok');
Route::get('/monitoring/kas', [MonitoringController::class, 'kas'])->name('superadmin.monitoring.kas');
Route::get('/monitoring/absensi', [MonitoringController::class, 'absensi'])->name('superadmin.monitoring.absensi');
Route::get('/monitoring/audit-log', [MonitoringController::class, 'auditLog'])->name('superadmin.monitoring.audit');

// Pengaturan Global
Route::get('/pengaturan/produk', [PengaturanController::class, 'produk'])->name('superadmin.pengaturan.produk');
Route::get('/pengaturan/printer', [PengaturanController::class, 'printer'])->name('superadmin.pengaturan.printer');
Route::get('/pengaturan/pajak', [PengaturanController::class, 'pajak'])->name('superadmin.pengaturan.pajak');
Route::get('/pengaturan/qr', [PengaturanController::class, 'qr'])->name('superadmin.pengaturan.qr');
