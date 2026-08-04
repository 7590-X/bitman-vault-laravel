<?php

/**
 * Manejador del caso de uso para confirmar el código MFA y activar el 2FA.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\ConfiguracionMfa;
use App\Domain\Puertos\RepositorioMfa;
use RuntimeException;

final class ManejadorConfirmarMfa
{
    public function __construct(
        private readonly RepositorioMfa $repositorioMfa,
    ) {}

    /**
     * Valida el código entregado y activa el doble factor si es correcto.
     *
     * @throws RuntimeException Si la configuración no existe o el código es inválido.
     */
    public function manejar(ComandoConfirmarMfa $comando): array
    {
        $configuracion = $this->repositorioMfa->buscarConfiguracionPorUsuarioId($comando->usuarioId);

        if ($configuracion === null) {
            throw new RuntimeException('No se ha iniciado la configuración de MFA para este usuario.');
        }

        $codigoValido = $this->repositorioMfa->obtenerUltimoCodigoValido($configuracion->obtenerId());

        if ($codigoValido === null || $codigoValido->obtenerCodigo() !== trim($comando->codigo)) {
            throw new RuntimeException('El código de verificación ingresado es incorrecto o ha expirado.');
        }

        // Marcar el código como usado
        $this->repositorioMfa->marcarCodigoComoUsado($codigoValido->obtenerId());

        // Activar la configuración MFA
        $configuracionActivada = new ConfiguracionMfa(
            id:                $configuracion->obtenerId(),
            usuarioId:         $configuracion->obtenerUsuarioId(),
            secretoEncriptado: $configuracion->obtenerSecretoEncriptado(),
            metodoEnvio:       $configuracion->obtenerMetodoEnvio(),
            esActivo:          true,
            activadoEn:        $configuracion->obtenerActivadoEn(),
        );

        $this->repositorioMfa->actualizarConfiguracion($configuracionActivada);

        return [
            'mensaje'   => 'Segundo factor de autenticación (MFA) activado exitosamente.',
            'es_activo' => true,
        ];
    }
}
