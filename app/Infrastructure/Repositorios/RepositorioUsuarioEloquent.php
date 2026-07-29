<?php

/**
 * Implementación Eloquent del repositorio de usuarios.
 * Adaptador de infraestructura que cumple el puerto RepositorioUsuario.
 */

namespace App\Infrastructure\Repositorios;

use App\Domain\Entidades\Usuario;
use App\Domain\Enums\EstadoUsuario;
use App\Domain\Puertos\RepositorioUsuario;
use App\Infrastructure\Modelos\UsuarioModelo;

final class RepositorioUsuarioEloquent implements RepositorioUsuario
{
    /**
     * Persiste una entidad Usuario en la base de datos mediante Eloquent.
     */
    public function guardar(Usuario $usuario): void
    {
        UsuarioModelo::create([
            'nombre_completo'    => $usuario->obtenerNombreCompleto(),
            'correo_electronico' => $usuario->obtenerCorreoElectronico(),
            'hash_contrasena'    => $usuario->obtenerHashContrasena(),
            'sal_contrasena'     => $usuario->obtenerSalContrasena(),
            'estado'             => $usuario->obtenerEstado()->value,
        ]);
    }

    /**
     * Verifica si un correo electrónico ya existe en la tabla de usuarios.
     */
    public function existeCorreo(string $correo): bool
    {
        return UsuarioModelo::where('correo_electronico', $correo)->exists();
    }

    /**
     * Busca un usuario por correo electrónico y lo mapea a la entidad de dominio.
     * Retorna null si no se encuentra ningún registro con ese correo.
     */
    public function buscarPorCorreo(string $correo): ?Usuario
    {
        /** @var UsuarioModelo|null $modelo */
        $modelo = UsuarioModelo::where('correo_electronico', $correo)->first();

        if ($modelo === null) {
            return null;
        }

        return new Usuario(
            id:                 $modelo->id,
            nombreCompleto:     $modelo->nombre_completo,
            correoElectronico:  $modelo->correo_electronico,
            hashContrasena:     $modelo->hash_contrasena,
            salContrasena:      $modelo->sal_contrasena,
            estado:             $modelo->estado instanceof EstadoUsuario
                                    ? $modelo->estado
                                    : EstadoUsuario::from($modelo->estado),
        );
    }
}

