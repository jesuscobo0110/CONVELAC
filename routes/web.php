<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Buzón del Admin (recepción)
Route::get('/buzon-recepcion', function () {
    return view('buzon.recepcion');
})->middleware('auth')->name('buzon.recepcion');

// Buzón de los clientes (envío)
Route::get('/buzon-envio', function () {
    return view('buzon.envio');
})->middleware('auth')->name('buzon.envio');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
