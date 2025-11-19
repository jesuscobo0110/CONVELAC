<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

use App\Http\Controllers\BuzonController;  // ← agrega esta línea arriba del todo

Route::middleware('auth')->group(function () {
    Route::get('/buzon-recepcion', [BuzonController::class, 'recepcion'])
        ->name('buzon.recepcion');

    Route::get('/buzon-envio', [BuzonController::class, 'envio'])
         ->name('buzon.envio');
        Route::post('/comprobantes', [App\Http\Controllers\ComprobanteController::class, 'store'])
        ->name('comprobante.store');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';