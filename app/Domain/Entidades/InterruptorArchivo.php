<?php

/**
 * Entidad de dominio que representa la tabla puente N:M entre
 * interruptores de emergencia y los archivos que protegen.
 * Corresponde a la tabla secretos.interruptor_archivos.
 */

namespace App\Domain\Entidades;

final class InterruptorArchivo
{
    /**
     * Crea una nueva instancia de la relación interruptor-archivo.
     *
     * @param int $interruptorId  FK hacia secretos.interruptores_emergencia (parte de PK compuesta).
     * @param int $archivoId      FK hacia secretos.archivos (parte de PK compuesta).
     */
    public function __construct(
        private readonly int $interruptorId,
        private readonly int $archivoId,
    ) {}

    /** Retorna el ID del interruptor de emergencia. */
    public function obtenerInterruptorId(): int
    {
        return $this->interruptorId;
    }

    /** Retorna el ID del archivo protegido por el interruptor. */
    public function obtenerArchivoId(): int
    {
        return $this->archivoId;
    }
}
