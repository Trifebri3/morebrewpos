<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::post('/order/submit', [OrderController::class, 'submit'])->name('order.submit');
Route::get('/order/track/{invoice}', [OrderController::class, 'track'])->name('order.track');

Route::get('/absen', [AbsensiController::class, 'showPublicForm'])->name('absen');
Route::post('/absen', [AbsensiController::class, 'submitPublicAbsen'])->name('absen.submit');
Route::post('/absen/check', [AbsensiController::class, 'checkUser'])->name('absen.check');

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    } elseif ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'kasir') {
        return redirect()->route('kasir.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/impersonate/leave', function () {
    if (session()->has('impersonator_id')) {
        $originalId = session('impersonator_id');
        session()->forget('impersonator_id');
        auth()->loginUsingId($originalId);

        return redirect()->route('superadmin.dashboard');
    }

    return redirect('/dashboard');
})->middleware('auth')->name('impersonate.leave');

Route::middleware(['auth'])->group(function () {
    Route::prefix('superadmin')->group(base_path('routes/superadmin.php'));
    Route::prefix('admin')->group(base_path('routes/admin.php'));
    Route::prefix('kasir')->group(base_path('routes/kasir.php'));
});
