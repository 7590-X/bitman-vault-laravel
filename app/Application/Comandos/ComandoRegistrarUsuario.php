<?php

/**
 * DTO (Data Transfer Object) del comando para registrar un nuevo usuario.
 * Encapsula los datos del formulario de registro de forma inmutable.
 */

namespace App\Application\Comandos;

final readonly class ComandoRegistrarUsuario
{
    /**
     * Crea una nueva instancia del comando de registro de usuario.
     */
    public function __construct(
        public string $nombreCompleto,
        public string $correoElectronico,
        public string $contrasena,
    ) {}
}
