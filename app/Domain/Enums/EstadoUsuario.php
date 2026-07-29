<?php

/**
 * Enum que representa los estados posibles de un usuario en el sistema.
 * Sigue la especificación de la tabla `estado_usuario` en la base de datos.
 */

namespace App\Domain\Enums;

enum EstadoUsuario: string
{
    /** Usuario activo con acceso completo al sistema. */
    case Activo = 'activo';

    /** Usuario suspendido temporalmente por el administrador. */
    case Suspendido = 'suspendido';

    /** Usuario eliminado lógicamente del sistema. */
    case Eliminado = 'eliminado';
}
