<?php

/**
 * Manejador del comando para registrar un nuevo usuario en la bóveda.
 * Contiene la lógica de negocio del caso de uso de registro.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\Usuario;
use App\Domain\Enums\EstadoUsuario;
use App\Domain\Puertos\RepositorioUsuario;
use RuntimeException;

final class ManejadorRegistrarUsuario
{
    /**
     * Inyecta el repositorio de usuarios mediante inyección de dependencias.
     */
    public function __construct(
        private readonly RepositorioUsuario $repositorioUsuario,
    ) {}

    /**
     * Ejecuta el caso de uso de registro de usuario.
     *
     * @throws RuntimeException Si el correo ya está registrado.
     */
    public function manejar(ComandoRegistrarUsuario $comando): void
    {
        if ($this->repositorioUsuario->existeCorreo($comando->correoElectronico)) {
            throw new RuntimeException('Este correo electrónico ya está registrado.');
        }

        $sal = bin2hex(random_bytes(32));

        $hash = password_hash(
            $sal . $comando->contrasena,
            PASSWORD_BCRYPT,
            ['cost' => 12]
        );

        $usuario = new Usuario(
            id:                null,
            nombreCompleto:    $comando->nombreCompleto,
            correoElectronico: $comando->correoElectronico,
            hashContrasena:    $hash,
            salContrasena:     $sal,
            estado:            EstadoUsuario::Activo,
        );

        $this->repositorioUsuario->guardar($usuario);
    }
}
