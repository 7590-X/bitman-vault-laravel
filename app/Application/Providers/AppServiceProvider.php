<?php

/**
 * Proveedor principal de servicios de la aplicación.
 * Registra los bindings de inyección de dependencias del sistema.
 */

namespace App\Application\Providers;

use App\Domain\Puertos\AutenticacionRepositorio;
use App\Domain\Puertos\RepositorioUsuario;
use App\Infrastructure\Auth\AutenticacionRepositorioJwt;
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

        $this->app->bind(
            AutenticacionRepositorio::class,
            AutenticacionRepositorioJwt::class
        );

        $this->app->bind(
            \App\Domain\Puertos\RepositorioLogin::class,
            \App\Infrastructure\Repositorios\RepositorioLoginEloquent::class
        );

        $this->app->bind(
            \App\Domain\Puertos\RepositorioTarjeta::class,
            \App\Infrastructure\Repositorios\RepositorioTarjetaEloquent::class
        );

        $this->app->bind(
            \App\Domain\Puertos\RepositorioTipoTarjeta::class,
            \App\Infrastructure\Repositorios\RepositorioTipoTarjetaEloquent::class
        );

        $this->app->bind(
            \App\Domain\Puertos\RepositorioLlaveSsh::class,
            \App\Infrastructure\Repositorios\RepositorioLlaveSshEloquent::class
        );

        $this->app->bind(
            \App\Domain\Puertos\RepositorioMfa::class,
            \App\Infrastructure\Repositorios\RepositorioMfaEloquent::class
        );

        $this->app->bind(
            \App\Domain\Puertos\ServicioNotificacionMfa::class,
            \App\Infrastructure\Servicios\ServicioNotificacionMfaLog::class
        );

        $this->app->bind(
            \App\Domain\Puertos\RepositorioEnvioNotaSegura::class,
            \App\Infrastructure\Repositorios\RepositorioEnvioNotaSeguraEloquent::class
        );

        $this->app->bind(
            \App\Domain\Puertos\ServicioNotificacionCorreoNota::class,
            \App\Infrastructure\Servicios\ServicioNotificacionCorreoNotaLog::class
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
