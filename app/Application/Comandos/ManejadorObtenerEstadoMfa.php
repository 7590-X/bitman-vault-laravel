<?php

/**
 * Manejador del caso de uso para consultar el estado del MFA de un usuario.
 */

namespace App\Application\Comandos;

use App\Domain\Puertos\RepositorioMfa;

final class ManejadorObtenerEstadoMfa
{
    public function __construct(
        private readonly RepositorioMfa $repositorioMfa,
    ) {}

    /**
     * Retorna la información actual de configuración MFA del usuario.
     */
    public function manejar(int $usuarioId): array
    {
        $configuracion = $this->repositorioMfa->buscarConfiguracionPorUsuarioId($usuarioId);

        if ($configuracion === null) {
            return [
                'registrado'   => false,
                'es_activo'    => false,
                'metodo_envio' => null,
                'activado_en'  => null,
            ];
        }

        return [
            'registrado'   => true,
            'es_activo'    => $configuracion->esActivo(),
            'metodo_envio' => $configuracion->obtenerMetodoEnvio()->value,
            'activado_en'  => $configuracion->obtenerActivadoEn()?->format('Y-m-d H:i:s'),
        ];
    }
}
