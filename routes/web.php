<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest', 'no-cache'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/registro', [RegisterController::class, 'create'])->name('register');
    Route::post('/registro', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware(['auth', 'no-cache'])->group(function () {
    Route::get('/inicio', function () {
        return view('app.home');
    })->name('inicio');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
