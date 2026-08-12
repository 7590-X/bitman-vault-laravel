<?php

namespace App\Application\Comandos;

class ComandoEliminarLlaveSsh
{
    public function __construct(
        public readonly int $id,
        public readonly int $usuarioId
    ) {
    }
}
