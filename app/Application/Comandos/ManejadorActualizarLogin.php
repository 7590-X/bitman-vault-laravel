<?php

/**
 * Manejador del comando para actualizar un login existente.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\Login;
use App\Domain\Puertos\RepositorioLogin;
use RuntimeException;

final class ManejadorActualizarLogin
{
    public function __construct(
        private readonly RepositorioLogin $repositorioLogin,
    ) {}

    /**
     * Ejecuta el caso de uso de actualización de login.
     *
     * @throws RuntimeException Si el login no existe o no pertenece al usuario.
     */
    public function manejar(ComandoActualizarLogin $comando): Login
    {
        $loginExistente = $this->repositorioLogin->buscarPorId($comando->id);

        if ($loginExistente === null) {
            throw new RuntimeException('El registro de login no existe.', 404);
        }

        if ($loginExistente->obtenerUsuarioId() !== $comando->usuarioId) {
            throw new RuntimeException('No tienes permiso para modificar este recurso.', 403);
        }

        $loginActualizado = new Login(
            id:                   $comando->id,
            usuarioId:            $comando->usuarioId,
            nombreSitio:          $comando->nombreSitio,
            url:                  $comando->url,
            usuarioLogin:         $comando->usuarioLogin,
            contrasenaEncriptada: $comando->contrasenaEncriptada,
            notas:                $comando->notas,
            creadoEn:             $loginExistente->obtenerCreadoEn(),
        );

        return $this->repositorioLogin->guardar($loginActualizado);
    }
}
