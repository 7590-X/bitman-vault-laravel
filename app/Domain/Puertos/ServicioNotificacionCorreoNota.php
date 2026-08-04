<?php

/**
 * Puerto de servicio para la notificación de notas seguras por correo electrónico.
 */

namespace App\Domain\Puertos;

use App\Domain\Entidades\EnvioNotaSegura;

interface ServicioNotificacionCorreoNota
{
    /**
     * Envía el correo electrónico con el enlace único y código de apertura al correo destino.
     */
    public function enviarNotificacionAcceso(EnvioNotaSegura $nota, string $urlAcceso): void;
}
