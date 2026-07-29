<?php

/**
 * Puerto (interfaz) del repositorio de usuarios.
 * Define el contrato que debe cumplir cualquier implementación de infraestructura.
 */

namespace App\Domain\Puertos;

use App\Domain\Entidades\Usuario;

interface RepositorioUsuario
{
    /**
     * Persiste un nuevo usuario en el almacenamiento.
     */
    public function guardar(Usuario $usuario): void;

    /**
     * Verifica si un correo electrónico ya existe en el sistema.
     */
    public function existeCorreo(string $correo): bool;

    /**
     * Busca un usuario por su correo electrónico.
     * Retorna null si no existe ningún usuario con ese correo.
     */
    public function buscarPorCorreo(string $correo): ?Usuario;
}
