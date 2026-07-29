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
Route::prefix('auth')->middleware('jwt.auth')->group(function () {
    Route::post('logout',  [ControladorAutenticacion::class, 'logout'])
        ->name('api.auth.logout');
    Route::post('refresh', [ControladorAutenticacion::class, 'refresh'])
        ->name('api.auth.refresh');
    Route::get('me',       [ControladorAutenticacion::class, 'me'])
        ->name('api.auth.me');
});
