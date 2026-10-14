<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::middleware(['guest', 'no-cache'])->group(function () { 
    Route::get('/', function () {
    return view('welcome');
    })->name('welcome');  
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'no-cache'])->group(function () {
    Route::get('/inicio', function () {
        return view('app.home');
    })->name('inicio');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
