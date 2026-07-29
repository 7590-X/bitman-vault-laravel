<?php

/**
 * DTO Comando para la actualización de un login existente.
 */

namespace App\Application\Comandos;

final class ComandoActualizarLogin
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $usuarioId,
        public readonly string  $nombreSitio,
        public readonly ?string $url,
        public readonly ?string $usuarioLogin,
        public readonly string  $contrasenaEncriptada,
        public readonly ?string $notas = null,
    ) {}
}
