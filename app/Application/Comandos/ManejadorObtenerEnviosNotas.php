<?php

/**
 * Manejador del caso de uso para obtener las notas seguras enviadas por el usuario autenticado.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\EnvioNotaSegura;
use App\Domain\Puertos\RepositorioEnvioNotaSegura;

final class ManejadorObtenerEnviosNotas
{
    public function __construct(
        private readonly RepositorioEnvioNotaSegura $repositorio,
    ) {}

    /**
     * @return EnvioNotaSegura[]
     */
    public function manejar(int $usuarioId): array
    {
        return $this->repositorio->buscarPorUsuarioId($usuarioId);
    }
}
