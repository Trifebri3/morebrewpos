<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;

/*
|--------------------------------------------------------------------------
| API Routes for MoreBrew SuperApp & POS Mobile Sync
|--------------------------------------------------------------------------
*/

Route::get('/sync', [SyncController::class, 'pull'])->name('api.sync.pull');
Route::post('/sync', [SyncController::class, 'push'])->name('api.sync.push');
Route::post('/login', [SyncController::class, 'login'])->name('api.login');
Route::get('/orders/pending', [SyncController::class, 'getPendingTableOrders'])->name('api.orders.pending');
Route::post('/orders/{invoice}/pay', [SyncController::class, 'payTableOrder'])->name('api.orders.pay');

// Pengaturan Kedai & Pajak (Sync Langsung dari Mobile POS / SuperApp)
Route::get('/settings', [SyncController::class, 'getSettings'])->name('api.settings.get');
Route::post('/settings', [SyncController::class, 'updateSettings'])->name('api.settings.update');

