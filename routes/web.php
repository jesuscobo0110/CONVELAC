<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComprobanteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/buzon-envio', [ComprobanteController::class, 'envio'])->name('buzon.envio');
    Route::post('/comprobante', [ComprobanteController::class, 'store'])->name('comprobante.store');
    Route::get('/buzon-recepcion', [ComprobanteController::class, 'recepcion'])->name('buzon.recepcion');

    Route::get('/comprobante/{comprobante}/{publicId}/ver', [ComprobanteController::class, 'marcarVisto'])
         ->name('comprobante.ver')
         ->where('publicId', '.*');

    Route::get('/comprobante/download/{filename}', [ComprobanteController::class, 'download'])
         ->name('comprobante.download')
         ->where('filename', '.*');

    // AQUÍ VAN LAS RUTAS QUE FALTABAN
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';