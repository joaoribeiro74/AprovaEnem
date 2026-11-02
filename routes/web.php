<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\Question;
use Illuminate\Http\Request;
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

if (app()->environment('local')) {
    Route::get('/dev/questions/{year}', function (Request $request, int $year) {
        $questions = Question::with('alternatives')
            ->where('year', $year)
            ->when($request->query('language'), function ($query, $language) {
                $language === 'any'
                    ? $query->whereNotNull('language')
                    : $query->where('language', $language);
            })
            ->when($request->query('from'), fn ($query, $number) => $query->where('number', '>=', $number))
            ->when($request->query('to'), fn ($query, $number) => $query->where('number', '<=', $number))
            ->orderBy('number')
            ->orderBy('language')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return view('dev.questions', compact('questions', 'year'));
    });
}
