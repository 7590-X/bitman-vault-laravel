<?php

/**
 * Entidad de dominio que representa un interruptor de emergencia (dead man switch).
 * Si el usuario no confirma su actividad dentro del intervalo definido,
 * el sistema envía el contenido protegido a los destinatarios configurados.
 * Corresponde a la tabla secretos.interruptores_emergencia.
 */

namespace App\Domain\Entidades;

use App\Domain\Enums\EstadoInterruptor;
use DateTimeImmutable;
use DateInterval;

final class InterruptorEmergencia
{
    /**
     * Crea una nueva instancia de la entidad InterruptorEmergencia.
     *
     * @param int|null               $id                    Nulo al crear; asignado por la BD al persistir.
     * @param int                    $usuarioId             FK hacia secretos.usuarios.
     * @param string                 $nombre                Nombre descriptivo del interruptor.
     * @param DateInterval           $intervaloConfirmacion Frecuencia requerida de confirmación.
     * @param string|null            $tokenConfirmacion     UUID secreto del usuario para confirmar actividad.
     * @param DateTimeImmutable      $confirmadoEn          Última vez que el usuario confirmó actividad.
     * @param DateTimeImmutable      $venceEn               Límite para la próxima confirmación.
     * @param EstadoInterruptor      $estado                Estado actual del interruptor.
     * @param DateTimeImmutable|null $creadoEn              Timestamp de creación (lo asigna la BD).
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $usuarioId,
        private readonly string             $nombre,
        private readonly DateInterval       $intervaloConfirmacion,
        private readonly ?string            $tokenConfirmacion,
        private readonly DateTimeImmutable  $confirmadoEn,
        private readonly DateTimeImmutable  $venceEn,
        private readonly EstadoInterruptor  $estado     = EstadoInterruptor::Activo,
        private readonly ?DateTimeImmutable $creadoEn   = null,
    ) {}

    /** Retorna el identificador del interruptor (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del usuario propietario. */
    public function obtenerUsuarioId(): int
    {
        return $this->usuarioId;
    }

    /** Retorna el nombre descriptivo del interruptor. */
    public function obtenerNombre(): string
    {
        return $this->nombre;
    }

    /** Retorna el intervalo requerido de confirmación. */
    public function obtenerIntervaloConfirmacion(): DateInterval
    {
        return $this->intervaloConfirmacion;
    }

    /** Retorna el token UUID para confirmar actividad. */
    public function obtenerTokenConfirmacion(): ?string
    {
        return $this->tokenConfirmacion;
    }

    /** Retorna el timestamp de la última confirmación del usuario. */
    public function obtenerConfirmadoEn(): DateTimeImmutable
    {
        return $this->confirmadoEn;
    }

    /** Retorna el timestamp límite para la próxima confirmación. */
    public function obtenerVenceEn(): DateTimeImmutable
    {
        return $this->venceEn;
    }

    /** Retorna el estado actual del interruptor. */
    public function obtenerEstado(): EstadoInterruptor
    {
        return $this->estado;
    }

    /** Retorna el timestamp de creación (null antes de persistir). */
    public function obtenerCreadoEn(): ?DateTimeImmutable
    {
        return $this->creadoEn;
    }

    /** Indica si el interruptor ha vencido (superó el límite sin confirmación). */
    public function estaVencido(): bool
    {
        return new DateTimeImmutable() > $this->venceEn
            && $this->estado === EstadoInterruptor::Activo;
    }
}
