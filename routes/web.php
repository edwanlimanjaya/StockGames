<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Financial\LiteracyController;
use App\Http\Controllers\Spiritual\SpiritualController;
use App\Http\Controllers\Game\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.register');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/financial-literacy', [LiteracyController::class, 'index'])->name('financial-literacy');
    Route::post('/financial-literacy/submit', [LiteracyController::class, 'submit'])->name('financial-literacy.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/spiritual', [SpiritualController::class, 'index'])->name('spiritual');
    Route::post('/spiritual/submit', [SpiritualController::class, 'submit'])->name('spiritual.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/game-session/{session}', [GameController::class, 'index'])->name('game-session');
    Route::post('/game-session/submit', [GameController::class, 'submit'])->name('game-session.submit');
});


require __DIR__.'/auth.php';
