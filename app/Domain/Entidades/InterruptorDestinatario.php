<?php

/**
 * Entidad de dominio que representa un destinatario que recibirá
 * el contenido protegido si el interruptor de emergencia se dispara.
 * Corresponde a la tabla secretos.interruptor_destinatarios.
 */

namespace App\Domain\Entidades;

final class InterruptorDestinatario
{
    /**
     * Crea una nueva instancia de la entidad InterruptorDestinatario.
     *
     * @param int|null    $id                  Nulo al crear; asignado por la BD al persistir.
     * @param int         $interruptorId       FK hacia secretos.interruptores_emergencia.
     * @param string      $correoDestinatario  Correo electrónico al que se enviará el contenido.
     * @param string|null $nombreDestinatario  Nombre de referencia del destinatario (opcional).
     */
    public function __construct(
        private readonly ?int    $id,
        private readonly int     $interruptorId,
        private readonly string  $correoDestinatario,
        private readonly ?string $nombreDestinatario = null,
    ) {}

    /** Retorna el identificador del destinatario (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del interruptor de emergencia al que pertenece. */
    public function obtenerInterruptorId(): int
    {
        return $this->interruptorId;
    }

    /** Retorna el correo electrónico del destinatario. */
    public function obtenerCorreoDestinatario(): string
    {
        return $this->correoDestinatario;
    }

    /** Retorna el nombre de referencia del destinatario (puede ser null). */
    public function obtenerNombreDestinatario(): ?string
    {
        return $this->nombreDestinatario;
    }
}
