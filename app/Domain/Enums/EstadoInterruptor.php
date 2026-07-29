<?php

/**
 * Enum que representa el estado de un interruptor de emergencia
 * (dead man switch) en el sistema.
 * Mapea el tipo ENUM secretos.estado_interruptor de PostgreSQL.
 */

namespace App\Domain\Enums;

enum EstadoInterruptor: string
{
    /** El interruptor está activo y esperando confirmaciones periódicas. */
    case Activo = 'activo';

    /** El interruptor fue pausado manualmente por el usuario. */
    case Pausado = 'pausado';

    /** El intervalo de confirmación expiró y el interruptor se disparó. */
    case Disparado = 'disparado';
}
