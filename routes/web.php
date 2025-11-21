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

    // Página de envío del cliente
    Route::get('/buzon-envio', [ComprobanteController::class, 'envio'])->name('buzon.envio');
    
    // Subir comprobantes
    Route::post('/comprobante', [ComprobanteController::class, 'store'])->name('comprobante.store');

    // Buzón de recepción (admin o quien reciba)
    Route::get('/buzon-recepcion', [ComprobanteController::class, 'recepcion'])->name('buzon.recepcion');

    // NUEVA RUTA: marcar como visto + abrir el archivo
    Route::get('/comprobante/{comprobante}/{publicId}/ver', [ComprobanteController::class, 'marcarVisto'])
         ->name('comprobante.ver');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';