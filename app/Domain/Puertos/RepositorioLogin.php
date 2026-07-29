<?php

/**
 * Puerto (interfaz) del repositorio de credenciales de acceso (logins).
 * Define el contrato de persistenia para la entidad de dominio Login.
 */

namespace App\Domain\Puertos;

use App\Domain\Entidades\Login;

interface RepositorioLogin
{
    /**
     * Persiste un login (creación o actualización).
     * Retorna la instancia de Login con su ID asignado en caso de creación.
     */
    public function guardar(Login $login): Login;

    /**
     * Busca un login por su identificador único.
     * Retorna null si no se encuentra.
     */
    public function buscarPorId(int $id): ?Login;

    /**
     * Retorna todos los logins pertenecientes al usuario especificado.
     *
     * @param int $usuarioId ID del usuario propietario.
     * @return array<Login> Lista de entidades de login.
     */
    public function obtenerTodosPorUsuario(int $usuarioId): array;

    /**
     * Elimina un login por su ID.
     * Retorna true si se eliminó correctamente, false en caso contrario.
     */
    public function eliminar(int $id): bool;
}
