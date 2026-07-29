<?php

/**
 * Entidad de dominio que representa el envío de una nota segura
 * entre dos cuentas de usuario.
 * Corresponde a la tabla secretos.notas_compartidas.
 */

namespace App\Domain\Entidades;

use App\Domain\Enums\EstadoNotaCompartida;
use DateTimeImmutable;

final class NotaCompartida
{
    /**
     * Crea una nueva instancia de la entidad NotaCompartida.
     *
     * @param int|null                $id                 Nulo al crear; asignado por la BD al persistir.
     * @param int                     $notaId             FK hacia secretos.notas_seguras.
     * @param int                     $usuarioOrigenId    FK hacia secretos.usuarios (quien envía).
     * @param int                     $usuarioDestinoId   FK hacia secretos.usuarios (quien recibe).
     * @param EstadoNotaCompartida    $estado             Estado actual del envío.
     * @param DateTimeImmutable|null  $enviadoEn          Timestamp de envío (lo asigna la BD).
     * @param DateTimeImmutable|null  $leidoEn            Timestamp de lectura por el destinatario (null si no leída).
     */
    public function __construct(
        private readonly ?int                  $id,
        private readonly int                   $notaId,
        private readonly int                   $usuarioOrigenId,
        private readonly int                   $usuarioDestinoId,
        private readonly EstadoNotaCompartida  $estado     = EstadoNotaCompartida::Enviada,
        private readonly ?DateTimeImmutable    $enviadoEn  = null,
        private readonly ?DateTimeImmutable    $leidoEn    = null,
    ) {}

    /** Retorna el identificador del envío (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID de la nota compartida. */
    public function obtenerNotaId(): int
    {
        return $this->notaId;
    }

    /** Retorna el ID del usuario que envía la nota. */
    public function obtenerUsuarioOrigenId(): int
    {
        return $this->usuarioOrigenId;
    }

    /** Retorna el ID del usuario destinatario. */
    public function obtenerUsuarioDestinoId(): int
    {
        return $this->usuarioDestinoId;
    }

    /** Retorna el estado actual del envío. */
    public function obtenerEstado(): EstadoNotaCompartida
    {
        return $this->estado;
    }

    /** Retorna el timestamp de envío (null antes de persistir). */
    public function obtenerEnviadoEn(): ?DateTimeImmutable
    {
        return $this->enviadoEn;
    }

    /** Retorna el timestamp de lectura (null si el destinatario aún no la ha leído). */
    public function obtenerLeidoEn(): ?DateTimeImmutable
    {
        return $this->leidoEn;
    }

    /** Indica si el destinatario ya leyó la nota. */
    public function fueLeida(): bool
    {
        return $this->estado === EstadoNotaCompartida::Leida;
    }
}
