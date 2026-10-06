<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;

/*
|--------------------------------------------------------------------------
| API Routes for MoreBrew SuperApp & POS Mobile Sync
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'MoreBrew API is running.',
        'version' => '1.0'
    ]);
});

Route::get('/sync', [SyncController::class, 'pull'])->name('api.sync.pull');
Route::post('/sync', [SyncController::class, 'push'])->name('api.sync.push');
Route::post('/sync-v2', [SyncController::class, 'pushV2'])->name('api.sync.push.v2'); // Endpoint baru yang lebih aman
Route::post('/login', [SyncController::class, 'login'])->name('api.login');
Route::get('/orders/pending', [SyncController::class, 'getPendingTableOrders'])->name('api.orders.pending');
Route::post('/orders/{invoice}/pay', [SyncController::class, 'payTableOrder'])->name('api.orders.pay');

// Pengaturan Kedai & Pajak (Sync Langsung dari Mobile POS / SuperApp)
Route::get('/settings', [SyncController::class, 'getSettings'])->name('api.settings.get');
Route::post('/settings', [SyncController::class, 'updateSettings'])->name('api.settings.update');

// Diagnostic & Ping Endpoints untuk Developer Tools
Route::get('/ping', [SyncController::class, 'ping'])->name('api.ping');
Route::get('/diagnostic', [SyncController::class, 'diagnostic'])->name('api.diagnostic');


