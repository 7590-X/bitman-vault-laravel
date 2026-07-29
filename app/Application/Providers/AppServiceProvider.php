<?php

/**
 * Proveedor principal de servicios de la aplicación.
 * Registra los bindings de inyección de dependencias del sistema.
 */

namespace App\Application\Providers;

use App\Domain\Puertos\RepositorioUsuario;
use App\Infrastructure\Repositorios\RepositorioUsuarioEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra los bindings de dependencias en el contenedor de servicios.
     */
    public function register(): void
    {
        $this->app->bind(
            RepositorioUsuario::class,
            RepositorioUsuarioEloquent::class
        );
    }

    /**
     * Inicializa cualquier servicio de la aplicación.
     */
    public function boot(): void
    {
        //
    }
}
