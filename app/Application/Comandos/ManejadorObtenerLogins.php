<?php

/**
 * Manejador del caso de uso para listar todos los logins de un usuario.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\Login;
use App\Domain\Puertos\RepositorioLogin;

final class ManejadorObtenerLogins
{
    public function __construct(
        private readonly RepositorioLogin $repositorioLogin,
    ) {}

    /**
     * Ejecuta la consulta para obtener los logins pertenecientes a un usuario.
     *
     * @param int $usuarioId ID del usuario autenticado.
     * @return array<Login>
     */
    public function manejar(int $usuarioId): array
    {
        return $this->repositorioLogin->obtenerTodosPorUsuario($usuarioId);
    }
}
