<?php

/**
 * DTO que encapsula los datos requeridos para eliminar una tarjeta.
 */

namespace App\Application\Comandos;

final readonly class ComandoEliminarTarjeta
{
    public function __construct(
        public int $id,
        public int $usuarioId,
    ) {}
}
