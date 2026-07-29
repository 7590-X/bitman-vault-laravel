<?php

/**
 * Entidad de dominio que representa a un Usuario de la bóveda.
 * Contiene los datos puros del negocio, sin dependencias de infraestructura.
 */

namespace App\Domain\Entidades;

use App\Domain\Enums\EstadoUsuario;

final class Usuario
{
    /**
     * Crea una nueva instancia de la entidad Usuario.
     *
     * @param int|null     $id                 Nulo al crear; asignado por la BD al persistir.
     * @param string       $nombreCompleto     Nombre completo del titular.
     * @param string       $correoElectronico  Correo único de acceso.
     * @param string       $hashContrasena     Hash de la contraseña maestra.
     * @param string       $salContrasena      Sal criptográfica usada en el hash.
     * @param EstadoUsuario $estado            Estado actual de la cuenta.
     */
    public function __construct(
        private readonly ?int          $id,
        private readonly string        $nombreCompleto,
        private readonly string        $correoElectronico,
        private readonly string        $hashContrasena,
        private readonly string        $salContrasena,
        private readonly EstadoUsuario $estado = EstadoUsuario::Activo,
    ) {}

    /** Retorna el ID del usuario (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el nombre completo del usuario. */
    public function obtenerNombreCompleto(): string
    {
        return $this->nombreCompleto;
    }

    /** Retorna el correo electrónico del usuario. */
    public function obtenerCorreoElectronico(): string
    {
        return $this->correoElectronico;
    }

    /** Retorna el hash de la contraseña maestra. */
    public function obtenerHashContrasena(): string
    {
        return $this->hashContrasena;
    }

    /** Retorna la sal criptográfica de la contraseña. */
    public function obtenerSalContrasena(): string
    {
        return $this->salContrasena;
    }

    /** Retorna el estado actual del usuario. */
    public function obtenerEstado(): EstadoUsuario
    {
        return $this->estado;
    }
}
