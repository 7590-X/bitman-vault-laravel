<?php

/**
 * Entidad de dominio que representa un enlace de acceso controlado
 * para compartir un archivo con límite de descargas, contraseña y expiración.
 * Corresponde a la tabla secretos.archivos_compartidos.
 */

namespace App\Domain\Entidades;

use DateTimeImmutable;

final class ArchivoCompartido
{
    /**
     * Crea una nueva instancia de la entidad ArchivoCompartido.
     *
     * @param int|null          $id                       Nulo al crear; asignado por la BD al persistir.
     * @param int               $archivoId                FK hacia secretos.archivos.
     * @param int               $usuarioOrigenId          FK hacia secretos.usuarios (quien comparte).
     * @param int|null          $usuarioDestinoId         FK hacia secretos.usuarios (destinatario); null si es enlace público.
     * @param string|null       $hashContrasenaAcceso     Hash de la contraseña de acceso al enlace (opcional).
     * @param string|null       $tokenAcceso              UUID único de la URL de acceso (lo genera la BD por defecto).
     * @param int               $maxDescargas             Número máximo de descargas permitidas (default: 1).
     * @param int               $descargasRealizadas      Contador de descargas realizadas (default: 0).
     * @param DateTimeImmutable $expiraEn                 Fecha y hora de expiración del enlace.
     * @param DateTimeImmutable|null $creadoEn            Timestamp de creación (lo asigna la BD).
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $archivoId,
        private readonly int                $usuarioOrigenId,
        private readonly ?int               $usuarioDestinoId,
        private readonly ?string            $hashContrasenaAcceso,
        private readonly ?string            $tokenAcceso,
        private readonly DateTimeImmutable  $expiraEn,
        private readonly int                $maxDescargas        = 1,
        private readonly int                $descargasRealizadas = 0,
        private readonly ?DateTimeImmutable $creadoEn            = null,
    ) {}

    /** Retorna el identificador del enlace compartido (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del archivo compartido. */
    public function obtenerArchivoId(): int
    {
        return $this->archivoId;
    }

    /** Retorna el ID del usuario que comparte el archivo. */
    public function obtenerUsuarioOrigenId(): int
    {
        return $this->usuarioOrigenId;
    }

    /** Retorna el ID del usuario destinatario (null si es enlace público). */
    public function obtenerUsuarioDestinoId(): ?int
    {
        return $this->usuarioDestinoId;
    }

    /** Retorna el hash de la contraseña de acceso (null si sin contraseña). */
    public function obtenerHashContrasenaAcceso(): ?string
    {
        return $this->hashContrasenaAcceso;
    }

    /** Retorna el token UUID de la URL de acceso. */
    public function obtenerTokenAcceso(): ?string
    {
        return $this->tokenAcceso;
    }

    /** Retorna la fecha y hora de expiración del enlace. */
    public function obtenerExpiraEn(): DateTimeImmutable
    {
        return $this->expiraEn;
    }

    /** Retorna el número máximo de descargas permitidas. */
    public function obtenerMaxDescargas(): int
    {
        return $this->maxDescargas;
    }

    /** Retorna el número de descargas realizadas hasta el momento. */
    public function obtenerDescargasRealizadas(): int
    {
        return $this->descargasRealizadas;
    }

    /** Retorna el timestamp de creación del enlace (null antes de persistir). */
    public function obtenerCreadoEn(): ?DateTimeImmutable
    {
        return $this->creadoEn;
    }

    /** Indica si el enlace ha alcanzado su límite de descargas. */
    public function estaAgotado(): bool
    {
        return $this->descargasRealizadas >= $this->maxDescargas;
    }

    /** Indica si el enlace ha expirado. */
    public function estaExpirado(): bool
    {
        return new DateTimeImmutable() > $this->expiraEn;
    }
}
