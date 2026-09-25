<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/order', function (\Illuminate\Http\Request $request) {
    $hash = $request->query('meja');
    $meja = \App\Models\Meja::where('qr_hash', $hash)->first();
    
    if (!$meja) {
        return "Meja tidak ditemukan atau QR tidak valid.";
    }

    return "<h1>Selamat Datang!</h1><p>Anda berada di <b>{$meja->name}</b>.</p><p>Halaman Menu Digital / Self-Ordering akan segera hadir di sini.</p>";
});

Route::get('/absen', [\App\Http\Controllers\AbsensiController::class, 'showPublicForm'])->name('absen');
Route::post('/absen', [\App\Http\Controllers\AbsensiController::class, 'submitPublicAbsen']);

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
