<?php

use App\Http\Controllers\AktivitasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\KategoriController;
use Illuminate\Support\Facades\Route;

// ... (di dalam Route::middleware('auth')->group)

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