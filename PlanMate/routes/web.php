<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'login');
Route::view('/register', 'register');
Route::view('/dashboard', 'dashboard');
Route::view('/aktivitas', 'aktivitas');
Route::view('/goals', 'goals');
Route::view('/kategori', 'kategori');
Route::view('/profil', 'profil');
Route::redirect('/', '/dashboard');
Route::view('/pencarian', 'pencarian');
Route::view('/kalender', 'kalender');
Route::view('/deadline', 'deadline');