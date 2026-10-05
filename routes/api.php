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
