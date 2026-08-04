<?php

/**
 * Manejador del caso de uso para desactivar el MFA de un usuario.
 */

namespace App\Application\Comandos;

use App\Domain\Puertos\RepositorioMfa;

final class ManejadorDesactivarMfa
{
    public function __construct(
        private readonly RepositorioMfa $repositorioMfa,
    ) {}

    /**
     * Desactiva el segundo factor de autenticación para el usuario.
     */
    public function manejar(int $usuarioId): array
    {
        $this->repositorioMfa->desactivarConfiguracion($usuarioId);

        return [
            'mensaje'   => 'Segundo factor de autenticación (MFA) desactivado correctamente.',
            'es_activo' => false,
        ];
    }
}
