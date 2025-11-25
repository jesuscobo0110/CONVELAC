<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComprobanteController;
use Illuminate\Support\Facades\Route;

// Página de inicio pública (opcional, puede ser tu logo o redirigir directo al login)
Route::get('/', function () {
    return redirect()->route('buzon.recepcion');
});

// ║ return view('welcome');

// ═══════════════════════════════════════════════════════════════════
// TODAS LAS RUTAS PROTEGIDAS (requieren login)
// ═══════════════════════════════════════════════════════════════════
Route::middleware('auth')->group(function () {

    // === RUTAS PRINCIPALES DE TU APP ===
    Route::get('/buzon-envio', [ComprobanteController::class, 'envio'])
        ->name('buzon.envio');

    Route::get('/buzon-recepcion', [ComprobanteController::class, 'recepcion'])
        ->name('buzon.recepcion');

    Route::post('/comprobante', [ComprobanteController::class, 'store'])
        ->name('comprobante.store');

    // Marcar como visto al hacer clic en "VER ARCHIVO"
    Route::post('/comprobante/{comprobante}/{publicId}/marcar-visto', [ComprobanteController::class, 'marcarVistoAjax'])
    ->name('comprobante.marcar-visto')
    ->where('publicId', '.*'); // ← ESTO ES LO QUE FALTABA
       

    // Descarga (por si la vuelves a usar algún día)
    Route::get('/comprobante/download/{filename}', [ComprobanteController::class, 'download'])
        ->name('comprobante.download')
        ->where('filename', '.*');

    // === PERFIL (opcional – puedes borrarlo si nunca lo usas) ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ═══════════════════════════════════════════════════════════════════
// RUTAS DE AUTENTICACIÓN (login, registro, recuperar contraseña...)
// ═══════════════════════════════════════════════════════════════════
require __DIR__.'/auth.php';