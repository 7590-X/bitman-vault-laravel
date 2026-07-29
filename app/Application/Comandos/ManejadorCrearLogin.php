<?php

/**
 * Manejador del comando para registrar un nuevo login en la bóveda.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\Login;
use App\Domain\Puertos\RepositorioLogin;

final class ManejadorCrearLogin
{
    public function __construct(
        private readonly RepositorioLogin $repositorioLogin,
    ) {}

    /**
     * Ejecuta el caso de uso de creación de login.
     */
    public function manejar(ComandoCrearLogin $comando): Login
    {
        $login = new Login(
            id:                   null,
            usuarioId:            $comando->usuarioId,
            nombreSitio:          $comando->nombreSitio,
            url:                  $comando->url,
            usuarioLogin:         $comando->usuarioLogin,
            contrasenaEncriptada: $comando->contrasenaEncriptada,
            notas:                $comando->notas,
        );

        return $this->repositorioLogin->guardar($login);
    }
}
