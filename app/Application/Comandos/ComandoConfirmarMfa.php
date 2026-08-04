<?php

/**
 * Comando DTO para confirmar el código de verificación MFA.
 */

namespace App\Application\Comandos;

final class ComandoConfirmarMfa
{
    /**
     * @param int    $usuarioId ID del usuario autenticado.
     * @param string $codigo    Código de 6 dígitos enviado al usuario.
     */
    public function __construct(
        public readonly int    $usuarioId,
        public readonly string $codigo,
    ) {}
}
