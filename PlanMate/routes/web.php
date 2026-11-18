<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Halaman untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
// ---- Lampiran (tersimpan di MySQL & Storage)
    Route::view('/lampiran', 'lampiran');
    Route::get('/lampiran/data', [LampiranController::class, 'data']);
    Route::post('/lampiran', [LampiranController::class, 'store']);
    Route::get('/lampiran/{id}/download', [LampiranController::class, 'download'])->whereNumber('id');
    Route::put('/lampiran/{id}', [LampiranController::class, 'update'])->whereNumber('id');
    Route::delete('/lampiran/{id}', [LampiranController::class, 'destroy'])->whereNumber('id');
    
// Halaman yang wajib login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::view('/dashboard', 'dashboard');
    Route::view('/aktivitas', 'aktivitas');
    Route::view('/kategori', 'kategori');
    Route::view('/profil', 'profil');
    Route::view('/pencarian', 'pencarian');
    Route::view('/kalender', 'kalender');
    Route::view('/deadline', 'deadline');

    // ---- Goals (tersimpan di MySQL)
    Route::view('/goals', 'goals');
    Route::get('/goals/data', [GoalController::class, 'data']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::put('/goals/{id}', [GoalController::class, 'update'])->whereNumber('id');
    Route::delete('/goals/{id}', [GoalController::class, 'destroy'])->whereNumber('id');
    Route::patch('/goals/{id}/progress', [GoalController::class, 'progress'])->whereNumber('id');
    Route::patch('/goals/{id}/tahapan/{tahapanId}/toggle', [GoalController::class, 'toggleTahapan'])
        ->whereNumber('id')->whereNumber('tahapanId');
});
