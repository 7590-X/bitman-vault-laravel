<?php

/**
 * Implementación Eloquent del repositorio de tipos de tarjeta.
 * Consulta la base de datos y mapea hacia entidades de Dominio.
 */

namespace App\Infrastructure\Repositorios;

use App\Domain\Entidades\TipoTarjeta;
use App\Domain\Puertos\RepositorioTipoTarjeta;
use App\Infrastructure\Modelos\TipoTarjetaModelo;

class RepositorioTipoTarjetaEloquent implements RepositorioTipoTarjeta
{
    /**
     * Convierte un modelo Eloquent TipoTarjetaModelo a una entidad de dominio TipoTarjeta.
     */
    private function mapearADominio(TipoTarjetaModelo $modelo): TipoTarjeta
    {
        return new TipoTarjeta(
            id: $modelo->id,
            nombre: strtoupper($modelo->nombre),
        );
    }

    /**
     * Retorna todos los tipos de tarjeta disponibles en la base de datos.
     *
     * @return array<TipoTarjeta>
     */
    public function obtenerTodos(): array
    {
        $modelos = TipoTarjetaModelo::all();

        return $modelos->map(fn(TipoTarjetaModelo $modelo) => $this->mapearADominio($modelo))->toArray();
    }
}
