<?php

/**
 * Manejador del caso de uso de registro de MFA.
 * Genera el secreto seguro, emite un código de 6 dígitos y notifica al usuario.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\CodigoMfa;
use App\Domain\Entidades\ConfiguracionMfa;
use App\Domain\Puertos\RepositorioMfa;
use App\Domain\Puertos\ServicioNotificacionMfa;
use DateTimeImmutable;
use Illuminate\Support\Facades\Crypt;

final class ManejadorRegistrarMfa
{
    public function __construct(
        private readonly RepositorioMfa          $repositorioMfa,
        private readonly ServicioNotificacionMfa $servicioNotificacion,
    ) {}

    /**
     * Ejecuta el registro/configuración inicial de MFA.
     *
     * @return array Resumen del proceso de registro.
     */
    public function manejar(ComandoRegistrarMfa $comando): array
    {
        $configExistente = $this->repositorioMfa->buscarConfiguracionPorUsuarioId($comando->usuarioId);

        // Generar un secreto criptográfico de 32 caracteres (128 bits de entropía)
        $secretoPlano = bin2hex(random_bytes(16));
        $secretoEncriptado = Crypt::encryptString($secretoPlano);

        if ($configExistente === null) {
            $configuracion = new ConfiguracionMfa(
                id:                null,
                usuarioId:         $comando->usuarioId,
                secretoEncriptado: $secretoEncriptado,
                metodoEnvio:       $comando->metodoEnvio,
                esActivo:          false, // Pendiente de confirmación con el código
            );
            $configuracionGuardada = $this->repositorioMfa->guardarConfiguracion($configuracion);
        } else {
            $configuracion = new ConfiguracionMfa(
                id:                $configExistente->obtenerId(),
                usuarioId:         $comando->usuarioId,
                secretoEncriptado: $secretoEncriptado,
                metodoEnvio:       $comando->metodoEnvio,
                esActivo:          $configExistente->esActivo(),
                activadoEn:        $configExistente->obtenerActivadoEn(),
            );
            $this->repositorioMfa->actualizarConfiguracion($configuracion);
            $configuracionGuardada = $configuracion;
        }

        // Generar código numérico aleatorio de 6 dígitos
        $codigoNumerico = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiraEn = (new DateTimeImmutable())->modify('+10 minutes');

        $codigoMfa = new CodigoMfa(
            id:                 null,
            configuracionMfaId: $configuracionGuardada->obtenerId(),
            codigo:             $codigoNumerico,
            expiraEn:           $expiraEn,
            esUsado:            false,
        );

        $this->repositorioMfa->guardarCodigo($codigoMfa);

        // Enviar el código vía notificación
        $this->servicioNotificacion->enviarCodigo(
            usuarioId: $comando->usuarioId,
            codigo:    $codigoNumerico,
            metodo:    $comando->metodoEnvio
        );

        return [
            'mensaje'             => 'Código de verificación MFA enviado correctamente.',
            'metodo_envio'        => $comando->metodoEnvio->value,
            'expira_en_minutos'   => 10,
            'requiere_confirmar'  => true,
        ];
    }
}
