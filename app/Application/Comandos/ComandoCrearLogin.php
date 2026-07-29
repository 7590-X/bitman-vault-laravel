<?php

/**
 * DTO Comando para la creación de un nuevo registro de login.
 */

namespace App\Application\Comandos;

final class ComandoCrearLogin
{
    public function __construct(
        public readonly int     $usuarioId,
        public readonly string  $nombreSitio,
        public readonly ?string $url,
        public readonly ?string $usuarioLogin,
        public readonly string  $contrasenaEncriptada,
        public readonly ?string $notas = null,
    ) {}
}
