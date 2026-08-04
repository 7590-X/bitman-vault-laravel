<?php

/**
 * Comando DTO para aperturar una Nota Segura por token y código de verificación.
 */

namespace App\Application\Comandos;

final class ComandoAperturarNota
{
    public function __construct(
        public readonly string $tokenAcceso,
        public readonly string $codigoApertura,
    ) {}
}
