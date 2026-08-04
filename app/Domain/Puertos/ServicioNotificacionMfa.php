<?php

/**
 * Puerto (interfaz) para el servicio de entrega de códigos MFA.
 * Abstrae el canal físico de envío (Correo, SMS) del dominio.
 */

namespace App\Domain\Puertos;

use App\Domain\Enums\MetodoEnvioMfa;

interface ServicioNotificacionMfa
{
    /**
     * Entrega un código de verificación MFA de 6 dígitos al usuario.
     *
     * @param int            $usuarioId  ID del usuario destinatario.
     * @param string         $codigo     Código numérico de 6 dígitos.
     * @param MetodoEnvioMfa $metodo     Canal de envío (Correo, SMS).
     */
    public function enviarCodigo(int $usuarioId, string $codigo, MetodoEnvioMfa $metodo): void;
}
