<?php

/**
 * Comando DTO para solicitar el registro de MFA para un usuario.
 */

namespace App\Application\Comandos;

use App\Domain\Enums\MetodoEnvioMfa;

final class ComandoRegistrarMfa
{
    /**
     * @param int            $usuarioId   ID del usuario autenticado.
     * @param MetodoEnvioMfa $metodoEnvio Canal preferido para recibir el código MFA.
     */
    public function __construct(
        public readonly int            $usuarioId,
        public readonly MetodoEnvioMfa $metodoEnvio = MetodoEnvioMfa::Correo,
    ) {}
}
