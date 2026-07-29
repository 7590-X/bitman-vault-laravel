<?php

/**
 * Entidad de dominio que representa unas credenciales de acceso (login)
 * guardadas de forma cifrada en la bóveda del usuario.
 * Corresponde a la tabla secretos.logins.
 */

namespace App\Domain\Entidades;

use DateTimeImmutable;

final class Login
{
    /**
     * Crea una nueva instancia de la entidad Login.
     *
     * @param int|null          $id                    Nulo al crear; asignado por la BD al persistir.
     * @param int               $usuarioId             FK hacia secretos.usuarios.
     * @param string            $nombreSitio           Nombre descriptivo del sitio o servicio.
     * @param string|null       $url                   URL asociada al login (opcional).
     * @param string|null       $usuarioLogin          Usuario o correo del sitio (opcional).
     * @param string            $contrasenaEncriptada  Contraseña cifrada en capa de aplicación.
     * @param string|null       $notas                 Notas libres adicionales (opcional).
     * @param DateTimeImmutable|null $creadoEn         Timestamp de creación (lo asigna la BD).
     * @param DateTimeImmutable|null $actualizadoEn    Timestamp de última modificación.
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $usuarioId,
        private readonly string             $nombreSitio,
        private readonly ?string            $url,
        private readonly ?string            $usuarioLogin,
        private readonly string             $contrasenaEncriptada,
        private readonly ?string            $notas            = null,
        private readonly ?DateTimeImmutable $creadoEn         = null,
        private readonly ?DateTimeImmutable $actualizadoEn    = null,
    ) {}

    /** Retorna el identificador del login (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del usuario propietario. */
    public function obtenerUsuarioId(): int
    {
        return $this->usuarioId;
    }

    /** Retorna el nombre descriptivo del sitio o servicio. */
    public function obtenerNombreSitio(): string
    {
        return $this->nombreSitio;
    }

    /** Retorna la URL del sitio (puede ser null). */
    public function obtenerUrl(): ?string
    {
        return $this->url;
    }

    /** Retorna el usuario o correo del sitio (puede ser null). */
    public function obtenerUsuarioLogin(): ?string
    {
        return $this->usuarioLogin;
    }

    /** Retorna la contraseña cifrada. */
    public function obtenerContrasenaEncriptada(): string
    {
        return $this->contrasenaEncriptada;
    }

    /** Retorna las notas adicionales (puede ser null). */
    public function obtenerNotas(): ?string
    {
        return $this->notas;
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
