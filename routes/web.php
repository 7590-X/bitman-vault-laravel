<?php

/**
 * Archivo de rutas web de la aplicación.
 * Define todas las rutas accesibles por el navegador.
 */

use App\Presentation\Http\Controllers\ControladorRegistro;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard.index');
});

Route::get('/registro', [ControladorRegistro::class, 'mostrar'])->name('registro');
Route::post('/registro', [ControladorRegistro::class, 'registrar'])->name('registro.guardar');

// Ruta de inicio de sesión
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
