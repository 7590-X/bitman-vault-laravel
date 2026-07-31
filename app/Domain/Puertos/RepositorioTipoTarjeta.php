<?php

/**
 * Puerto (interfaz) del repositorio de tipos de tarjeta.
 * Define el contrato para acceder al catálogo de tipos de tarjeta.
 */

namespace App\Domain\Puertos;

use App\Domain\Entidades\TipoTarjeta;

interface RepositorioTipoTarjeta
{
    /**
     * Retorna todos los tipos de tarjeta disponibles en el catálogo.
     *
     * @return array<TipoTarjeta> Lista de entidades TipoTarjeta.
     */
    public function obtenerTodos(): array;
}
