<?php

/**
 * DTO (Data Transfer Object) del comando para iniciar sesión.
 * Encapsula las credenciales de acceso de forma inmutable.
 */

namespace App\Application\Comandos;

final readonly class ComandoIniciarSesion
{
    /**
     * Crea una nueva instancia del comando de login.
     *
     * @param string $correoElectronico Correo electrónico del usuario.
     * @param string $contrasena        Contraseña en texto plano (se verificará contra sal+hash).
     */
    public function __construct(
        public string $correoElectronico,
        public string $contrasena,
    ) {}
}
