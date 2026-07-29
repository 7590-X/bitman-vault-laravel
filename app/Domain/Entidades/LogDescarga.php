<?php

/**
 * Entidad de dominio que representa la bitácora de cada descarga
 * realizada sobre un enlace de archivo compartido.
 * Tabla particionada bimestralmente por descargado_en.
 * Corresponde a la tabla secretos.log_descargas.
 */

namespace App\Domain\Entidades;

use DateTimeImmutable;

final class LogDescarga
{
    /**
     * Crea una nueva instancia de la entidad LogDescarga.
     *
     * @param int|null          $id                    Nulo al crear; asignado por la BD al persistir.
     * @param int               $archivoCompartidoId   FK hacia secretos.archivos_compartidos.
     * @param string|null       $ipOrigen              Dirección IP desde donde se realizó la descarga (opcional).
     * @param DateTimeImmutable|null $descargadoEn     Timestamp de la descarga y clave de partición.
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $archivoCompartidoId,
        private readonly ?string            $ipOrigen     = null,
        private readonly ?DateTimeImmutable $descargadoEn = null,
    ) {}

    /** Retorna el identificador del evento de descarga (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del enlace compartido que fue descargado. */
    public function obtenerArchivoCompartidoId(): int
    {
        return $this->archivoCompartidoId;
    }

    /** Retorna la IP de origen de la descarga (puede ser null). */
    public function obtenerIpOrigen(): ?string
    {
        return $this->ipOrigen;
    }

    /** Retorna el timestamp de descarga y clave de partición (null antes de persistir). */
    public function obtenerDescargadoEn(): ?DateTimeImmutable
    {
        return $this->descargadoEn;
    }
}
