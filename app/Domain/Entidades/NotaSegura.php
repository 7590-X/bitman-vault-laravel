<?php

/**
 * Entidad de dominio que representa una nota de texto cifrada
 * guardada por un usuario en su bóveda.
 * Corresponde a la tabla secretos.notas_seguras.
 */

namespace App\Domain\Entidades;

use DateTimeImmutable;

final class NotaSegura
{
    /**
     * Crea una nueva instancia de la entidad NotaSegura.
     *
     * @param int|null          $id                    Nulo al crear; asignado por la BD al persistir.
     * @param int               $usuarioId             FK hacia secretos.usuarios.
     * @param string            $titulo                Título descriptivo de la nota.
     * @param string            $contenidoEncriptado   Contenido cifrado en capa de aplicación.
     * @param DateTimeImmutable|null $creadoEn         Timestamp de creación (lo asigna la BD).
     * @param DateTimeImmutable|null $actualizadoEn    Timestamp de última modificación.
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $usuarioId,
        private readonly string             $titulo,
        private readonly string             $contenidoEncriptado,
        private readonly ?DateTimeImmutable $creadoEn      = null,
        private readonly ?DateTimeImmutable $actualizadoEn = null,
    ) {}

    /** Retorna el identificador de la nota (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del usuario propietario. */
    public function obtenerUsuarioId(): int
    {
        return $this->usuarioId;
    }

    /** Retorna el título de la nota. */
    public function obtenerTitulo(): string
    {
        return $this->titulo;
    }

    /** Retorna el contenido cifrado de la nota. */
    public function obtenerContenidoEncriptado(): string
    {
        return $this->contenidoEncriptado;
    }

    /** Retorna el timestamp de creación (null antes de persistir). */
    public function obtenerCreadoEn(): ?DateTimeImmutable
    {
        return $this->creadoEn;
    }

    /** Retorna el timestamp de última modificación (null antes de persistir). */
    public function obtenerActualizadoEn(): ?DateTimeImmutable
    {
        return $this->actualizadoEn;
    }
}
