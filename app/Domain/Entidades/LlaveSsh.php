<?php

/**
 * Entidad de dominio que representa un par de llaves SSH almacenadas
 * de forma cifrada en la bóveda del usuario.
 * Corresponde a la tabla secretos.llaves_ssh.
 */

namespace App\Domain\Entidades;

use DateTimeImmutable;

final class LlaveSsh
{
    /**
     * Crea una nueva instancia de la entidad LlaveSsh.
     *
     * @param int|null          $id                       Nulo al crear; asignado por la BD al persistir.
     * @param int               $usuarioId                FK hacia secretos.usuarios.
     * @param string            $nombre                   Nombre descriptivo de la llave.
     * @param string            $llavePrivadaEncriptada   Llave privada cifrada en capa de aplicación.
     * @param string|null       $llavePublica             Llave pública en claro (opcional).
     * @param string|null       $frasePassEncriptada      Passphrase de la llave privada, cifrada (opcional).
     * @param DateTimeImmutable|null $creadoEn            Timestamp de creación (lo asigna la BD).
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $usuarioId,
        private readonly string             $nombre,
        private readonly string             $llavePrivadaEncriptada,
        private readonly ?string            $llavePublica           = null,
        private readonly ?string            $frasePassEncriptada    = null,
        private readonly ?DateTimeImmutable $creadoEn               = null,
    ) {}

    /** Retorna el identificador de la llave SSH (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del usuario propietario. */
    public function obtenerUsuarioId(): int
    {
        return $this->usuarioId;
    }

    /** Retorna el nombre descriptivo de la llave. */
    public function obtenerNombre(): string
    {
        return $this->nombre;
    }

    /** Retorna la llave privada cifrada. */
    public function obtenerLlavePrivadaEncriptada(): string
    {
        return $this->llavePrivadaEncriptada;
    }

    /** Retorna la llave pública en claro (puede ser null). */
    public function obtenerLlavePublica(): ?string
    {
        return $this->llavePublica;
    }

    /** Retorna la frase de paso cifrada (puede ser null si la llave no tiene passphrase). */
    public function obtenerFrasePassEncriptada(): ?string
    {
        return $this->frasePassEncriptada;
    }

    /** Retorna el timestamp de creación (null antes de persistir). */
    public function obtenerCreadoEn(): ?DateTimeImmutable
    {
        return $this->creadoEn;
    }
}
