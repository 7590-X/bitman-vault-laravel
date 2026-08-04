<?php

/**
 * Manejador del caso de uso para revocar/eliminar un Envío de Nota Segura.
 */

namespace App\Application\Comandos;

use App\Domain\Puertos\RepositorioEnvioNotaSegura;
use RuntimeException;

final class ManejadorEliminarEnvioNota
{
    public function __construct(
        private readonly RepositorioEnvioNotaSegura $repositorio,
    ) {}

    public function manejar(int $id, int $usuarioId): void
    {
        $eliminado = $this->repositorio->eliminar($id, $usuarioId);

        if (!$eliminado) {
            throw new RuntimeException('La nota no fue encontrada o no pertenece al usuario.', 404);
        }
    }
}
