<?php

/**
 * Manejador del comando (consulta) para obtener todas las tarjetas de un usuario.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\Tarjeta;
use App\Domain\Puertos\RepositorioTarjeta;

final class ManejadorObtenerTarjetas
{
    public function __construct(
        private readonly RepositorioTarjeta $repositorioTarjeta,
    ) {}

    /**
     * Ejecuta el caso de uso para obtener las tarjetas de un usuario.
     *
     * @param int $usuarioId
     * @return array<Tarjeta>
     */
    public function manejar(int $usuarioId): array
    {
        return $this->repositorioTarjeta->obtenerTodasPorUsuario($usuarioId);
    }
}
