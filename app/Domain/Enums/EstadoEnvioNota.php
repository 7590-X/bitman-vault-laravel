<?php

/**
 * Enum que representa los estados posibles de un envío de nota segura.
 */

namespace App\Domain\Enums;

enum EstadoEnvioNota: string
{
    case NO_APERTURADA = 'no_aperturada';
    case APERTURADA    = 'aperturada';
    case EXPIRADA      = 'expirada';
    case REVOCADA      = 'revocada';

    public function etiqueta(): string
    {
        return match ($this) {
            self::NO_APERTURADA => 'No Aperturada',
            self::APERTURADA    => 'Aperturada',
            self::EXPIRADA      => 'Expirada',
            self::REVOCADA      => 'Revocada',
        };
    }
}
