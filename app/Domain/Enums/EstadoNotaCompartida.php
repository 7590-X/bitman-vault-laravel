<?php

/**
 * Enum que representa el ciclo de vida de una nota segura
 * enviada entre cuentas de usuario.
 * Mapea el tipo ENUM secretos.estado_nota_compartida de PostgreSQL.
 */

namespace App\Domain\Enums;

enum EstadoNotaCompartida: string
{
    /** La nota fue enviada pero aún no ha sido leída por el destinatario. */
    case Enviada = 'enviada';

    /** El destinatario ha leído la nota compartida. */
    case Leida = 'leida';

    /** El remitente revocó el acceso a la nota antes de ser leída. */
    case Revocada = 'revocada';
}
