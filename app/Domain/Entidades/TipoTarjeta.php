<?php

/**
 * Entidad de dominio que representa un tipo de tarjeta del catálogo.
 * Catálogo inmutable con valores predefinidos (crédito / débito).
 * Corresponde a la tabla secretos.tipos_tarjeta.
 */

namespace App\Domain\Entidades;

final class TipoTarjeta
{
    /**
     * Crea una nueva instancia del tipo de tarjeta.
     *
     * @param int    $id     Identificador del tipo (SMALLINT IDENTITY).
     * @param string $nombre Nombre descriptivo del tipo (ej. 'credito', 'debito').
     */
    public function __construct(
        private readonly int    $id,
        private readonly string $nombre,
    ) {}

    /** Retorna el identificador del tipo de tarjeta. */
    public function obtenerIdTipo(): int
    {
        return $this->id;
    }

    /** Retorna el nombre del tipo de tarjeta. */
    public function obtenerNombre(): string
    {
        return $this->nombre;
    }
}
