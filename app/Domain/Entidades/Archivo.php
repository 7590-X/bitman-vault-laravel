<?php

/**
 * Entidad de dominio que representa los metadatos de un archivo cifrado
 * subido por un usuario. El binario real reside en almacenamiento externo.
 * Corresponde a la tabla secretos.archivos.
 */

namespace App\Domain\Entidades;

use DateTimeImmutable;

final class Archivo
{
    /**
     * Crea una nueva instancia de la entidad Archivo.
     *
     * @param int|null          $id                  Nulo al crear; asignado por la BD al persistir.
     * @param int               $usuarioId           FK hacia secretos.usuarios.
     * @param string            $nombreArchivo       Nombre original del archivo.
     * @param string            $rutaAlmacenamiento  Ruta o clave del objeto en el bucket/storage.
     * @param string            $hashSha256          Huella SHA-256 del archivo cifrado (64 chars hex).
     * @param int               $tamanioBytes        Tamaño en bytes (debe ser > 0).
     * @param bool              $esEncriptado        Indica si el archivo está cifrado en reposo.
     * @param DateTimeImmutable|null $subidoEn       Timestamp de subida (lo asigna la BD).
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $usuarioId,
        private readonly string             $nombreArchivo,
        private readonly string             $rutaAlmacenamiento,
        private readonly string             $hashSha256,
        private readonly int                $tamanioBytes,
        private readonly bool               $esEncriptado = true,
        private readonly ?DateTimeImmutable $subidoEn     = null,
    ) {}

    /** Retorna el identificador del archivo (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del usuario propietario. */
    public function obtenerUsuarioId(): int
    {
        return $this->usuarioId;
    }

    /** Retorna el nombre original del archivo. */
    public function obtenerNombreArchivo(): string
    {
        return $this->nombreArchivo;
    }

    /** Retorna la ruta de almacenamiento del archivo en el storage externo. */
    public function obtenerRutaAlmacenamiento(): string
    {
        return $this->rutaAlmacenamiento;
    }

    /** Retorna el hash SHA-256 del archivo cifrado. */
    public function obtenerHashSha256(): string
    {
        return $this->hashSha256;
    }

    /** Retorna el tamaño del archivo en bytes. */
    public function obtenerTamanioBytes(): int
    {
        return $this->tamanioBytes;
    }

    /** Indica si el archivo está cifrado en reposo. */
    public function esEncriptado(): bool
    {
        return $this->esEncriptado;
    }

    /** Retorna el timestamp de subida (null antes de persistir). */
    public function obtenerSubidoEn(): ?DateTimeImmutable
    {
        return $this->subidoEn;
    }
}
