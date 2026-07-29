<?php

/**
 * Manejador del comando para eliminar un login existente.
 */

namespace App\Application\Comandos;

use App\Domain\Puertos\RepositorioLogin;
use RuntimeException;

final class ManejadorEliminarLogin
{
    public function __construct(
        private readonly RepositorioLogin $repositorioLogin,
    ) {}

    /**
     * Ejecuta el caso de uso de eliminación de login.
     *
     * @throws RuntimeException Si el login no existe o no pertenece al usuario.
     */
    public function manejar(ComandoEliminarLogin $comando): void
    {
        $login = $this->repositorioLogin->buscarPorId($comando->id);

        if ($login === null) {
            throw new RuntimeException('El registro de login no existe.', 404);
        }

        if ($login->obtenerUsuarioId() !== $comando->usuarioId) {
            throw new RuntimeException('No tienes permiso para eliminar este recurso.', 403);
        }

        $this->repositorioLogin->eliminar($comando->id);
    }
}
