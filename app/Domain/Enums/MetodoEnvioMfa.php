<?php

/**
 * Enum que representa el canal de entrega del código MFA.
 * Sin apps externas; el sistema entrega el código directamente.
 * Mapea el tipo ENUM secretos.metodo_envio_mfa de PostgreSQL.
 */

namespace App\Domain\Enums;

enum MetodoEnvioMfa: string
{
    /** El código se envía al correo electrónico registrado del usuario. */
    case Correo = 'correo';

    /** El código se envía al número de teléfono del usuario vía SMS. */
    case Sms = 'sms';
}
