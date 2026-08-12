<?php

namespace App\Application\Comandos;

class ComandoGuardarLlaveSsh
{
    public function __construct(
        public readonly int $usuarioId,
        public readonly string $nombre,
        public readonly string $llavePrivadaEncriptada,
        public readonly string $llavePublica,
        public readonly ?string $frasePasoEncriptada = null
    ) {
    }
}
