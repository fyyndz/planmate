<?php

use App\Http\Controllers\AktivitasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\KategoriController;
use Illuminate\Support\Facades\Route;

// ... (di dalam Route::middleware('auth')->group)

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
});
    // ---- Kategori (CRUD Database)
    Route::view('/kategori', 'kategori');
    Route::get('/kategori/data', [KategoriController::class, 'data']);
    Route::post('/kategori', [KategoriController::class, 'store']);
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->whereNumber('id');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->whereNumber('id');

    // ---- Aktivitas (CRUD Database)
    Route::view('/aktivitas', 'aktivitas');
    Route::get('/aktivitas/data', [AktivitasController::class, 'data']);
    Route::post('/aktivitas', [AktivitasController::class, 'store']);
    Route::put('/aktivitas/{id}', [AktivitasController::class, 'update'])->whereNumber('id');
    Route::patch('/aktivitas/{id}/done', [AktivitasController::class, 'markDone'])->whereNumber('id');
    Route::delete('/aktivitas/{id}', [AktivitasController::class, 'destroy'])->whereNumber('id');
