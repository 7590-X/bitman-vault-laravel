<?php

/**
 * Implementación del servicio de notificación MFA.
 * Registra y envía el código de seguridad al usuario.
 */

namespace App\Infrastructure\Servicios;

use App\Domain\Enums\MetodoEnvioMfa;
use App\Domain\Puertos\ServicioNotificacionMfa;
use App\Infrastructure\Modelos\UsuarioModelo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class ServicioNotificacionMfaLog implements ServicioNotificacionMfa
{
    /**
     * Entrega el código MFA de 6 dígitos al usuario según el canal seleccionado.
     */
    public function enviarCodigo(int $usuarioId, string $codigo, MetodoEnvioMfa $metodo): void
    {
        /** @var UsuarioModelo|null $usuario */
        $usuario = UsuarioModelo::find($usuarioId);
        $destino = $usuario?->correo_electronico ?? "Usuario #{$usuarioId}";

        // Log seguro en entorno de auditoría
        Log::info("[MFA Notification] Código de autenticación enviado a {$destino} via {$metodo->value}: {$codigo}");

        // Envío por Correo Electrónico
        if ($metodo === MetodoEnvioMfa::Correo && $usuario?->correo_electronico) {
            try {
                Mail::raw("Tu código de verificación de 2 factores (MFA) para Bitman es: {$codigo}. Este código expira en 10 minutos.", function ($message) use ($usuario) {
                    $message->to($usuario->correo_electronico)
                        ->subject('Código de Verificación MFA - Bitman Vault');
                });
            } catch (\Throwable $e) {
                Log::warning("[MFA Notification] No se pudo enviar el correo a {$usuario->correo_electronico}: {$e->getMessage()}");
            }
        }
    }
}
