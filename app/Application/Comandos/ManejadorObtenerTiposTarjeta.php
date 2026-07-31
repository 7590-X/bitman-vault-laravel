<?php

/**
 * Manejador del comando (consulta) para obtener todos los tipos de tarjeta.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\TipoTarjeta;
use App\Domain\Puertos\RepositorioTipoTarjeta;

final class ManejadorObtenerTiposTarjeta
{
    public function __construct(
        private readonly RepositorioTipoTarjeta $repositorioTipoTarjeta,
    ) {}

    /**
     * Ejecuta el caso de uso (consulta) para obtener los tipos de tarjeta.
     *
     * @return array<TipoTarjeta>
     */
    public function manejar(): array
    {
        return $this->repositorioTipoTarjeta->obtenerTodos();
    }
}
