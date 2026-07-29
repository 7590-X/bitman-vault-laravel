<?php

/**
 * DTO Comando para eliminar un login existente.
 */

namespace App\Application\Comandos;

final class ComandoEliminarLogin
{
    public function __construct(
        public readonly int $id,
        public readonly int $usuarioId,
    ) {}
}
