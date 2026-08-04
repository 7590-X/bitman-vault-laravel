<?php

/**
 * Implementación de infraestructura para el envío de notificaciones por correo de notas seguras.
 * Utiliza las credenciales configuradas en el .env (soporta Brevo vía SMTP/Mailer) con fallback a log.
 */

namespace App\Infrastructure\Servicios;

use App\Domain\Entidades\EnvioNotaSegura;
use App\Domain\Puertos\ServicioNotificacionCorreoNota;
use App\Infrastructure\Mail\CorreoNotaSeguraMailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class ServicioNotificacionCorreoNotaLog implements ServicioNotificacionCorreoNota
{
    public function enviarNotificacionAcceso(EnvioNotaSegura $nota, string $urlAcceso): void
    {
        try {
            // Envío del correo electrónico utilizando la configuración nativa de Laravel Mail / Brevo
            Mail::to($nota->obtenerCorreoDestino())
                ->send(new CorreoNotaSeguraMailable($nota, $urlAcceso));

            Log::info("Correo de Nota Segura enviado exitosamente a {$nota->obtenerCorreoDestino()} vía Brevo/Mail.");
        } catch (Throwable $e) {
            // Fallback resiliente: registrar error en logs y registrar información para auditoría
            Log::error("No se pudo enviar el correo a {$nota->obtenerCorreoDestino()} vía Brevo: " . $e->getMessage());

            Log::info('--- REGISTRO DE FALLBACK EN LOG ---');
            Log::info('Para: ' . $nota->obtenerCorreoDestino());
            Log::info('Título: ' . $nota->obtenerTitulo());
            Log::info('URL Acceso: ' . $urlAcceso);
            Log::info('Código Apertura: ' . $nota->obtenerCodigoApertura());
            Log::info('-----------------------------------');
        }
    }
}
