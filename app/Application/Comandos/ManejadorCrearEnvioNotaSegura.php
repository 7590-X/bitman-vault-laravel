<?php

/**
 * Manejador del caso de uso para crear un Envío de Nota Segura.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\EnvioNotaSegura;
use App\Domain\Enums\EstadoEnvioNota;
use App\Domain\Puertos\RepositorioEnvioNotaSegura;
use App\Domain\Puertos\ServicioNotificacionCorreoNota;
use DateTimeImmutable;
use Illuminate\Support\Str;

final class ManejadorCrearEnvioNotaSegura
{
    public function __construct(
        private readonly RepositorioEnvioNotaSegura $repositorio,
        private readonly ServicioNotificacionCorreoNota $servicioCorreo,
    ) {}

    public function manejar(ComandoCrearEnvioNotaSegura $comando): EnvioNotaSegura
    {
        $tokenAcceso = (string) Str::uuid();

        // Generar código de apertura de 6 caracteres alfanuméricos si no se proporciona uno
        $codigoApertura = $comando->codigoApertura && trim($comando->codigoApertura) !== ''
            ? trim($comando->codigoApertura)
            : strtoupper(Str::random(6));

        $minutos = max(1, $comando->minutosExpiracion);
        $expiraEn = (new DateTimeImmutable())->modify("+{$minutos} minutes");

        $nota = new EnvioNotaSegura(
            id: null,
            usuarioId: $comando->usuarioId,
            titulo: trim($comando->titulo),
            correoDestino: trim(strtolower($comando->correoDestino)),
            contenidoEncriptado: $comando->textoNota,
            codigoApertura: $codigoApertura,
            tokenAcceso: $tokenAcceso,
            estado: EstadoEnvioNota::NO_APERTURADA,
            duracionVisualizacionSegundos: max(5, $comando->duracionVisualizacionSegundos),
            expiraEn: $expiraEn,
            aperturadoEn: null,
        );

        $notaGuardada = $this->repositorio->guardar($nota);

        // Notificar por correo (esqueleto extensible)
        $urlAcceso = url("/notas/aperturar/{$tokenAcceso}");
        $this->servicioCorreo->enviarNotificacionAcceso($notaGuardada, $urlAcceso);

        return $notaGuardada;
    }
}
