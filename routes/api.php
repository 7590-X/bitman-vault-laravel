<?php

/**
 * Rutas de la API REST de la aplicación.
 * Todas las rutas usan JSON; no hay sesiones ni cookies.
 *
 * Grupos:
 *   - Públicas: /api/auth/login  (no requieren token)
 *   - Protegidas: requieren token JWT en el encabezado Authorization: Bearer <token>
 */

use App\Presentation\Http\Controllers\ControladorAutenticacion;
use App\Presentation\Http\Controllers\ControladorLogin;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Rutas públicas de autenticación
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [ControladorAutenticacion::class, 'login'])
        ->name('api.auth.login');
});

// ─────────────────────────────────────────────────────────────────────────────
// Rutas protegidas — requieren token JWT válido
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('jwt.auth')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout',  [ControladorAutenticacion::class, 'logout'])
            ->name('api.auth.logout');
        Route::post('refresh', [ControladorAutenticacion::class, 'refresh'])
            ->name('api.auth.refresh');
        Route::get('me',       [ControladorAutenticacion::class, 'me'])
            ->name('api.auth.me');
    });

    Route::prefix('catalogos')->group(function () {
        Route::get('tipos-tarjeta',           [\App\Presentation\Http\Controllers\ControladorCatalogo::class, 'obtenerTiposTarjeta']);
    });

    Route::prefix('mfa')->group(function () {
        Route::get('estado',     [\App\Presentation\Http\Controllers\ControladorMfa::class, 'estado'])->name('api.mfa.estado');
        Route::post('registrar', [\App\Presentation\Http\Controllers\ControladorMfa::class, 'registrar'])->name('api.mfa.registrar');
        Route::post('confirmar', [\App\Presentation\Http\Controllers\ControladorMfa::class, 'confirmar'])->name('api.mfa.confirmar');
        Route::post('desactivar', [\App\Presentation\Http\Controllers\ControladorMfa::class, 'desactivar'])->name('api.mfa.desactivar');
    });

    Route::apiResource('logins', ControladorLogin::class)->except(['show']);
    Route::apiResource('tarjetas', App\Presentation\Http\Controllers\ControladorTarjeta::class)->except(['show']);
    Route::apiResource('llaves-ssh', App\Presentation\Http\Controllers\ControladorLlaveSsh::class)->except(['show', 'update']);
});
