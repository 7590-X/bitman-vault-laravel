<?php

/**
 * Implementación Eloquent del repositorio de credenciales de acceso (logins).
 * Adaptador de infraestructura que cumple el puerto RepositorioLogin.
 */

namespace App\Infrastructure\Repositorios;

use App\Domain\Entidades\Login;
use App\Domain\Puertos\RepositorioLogin;
use App\Infrastructure\Modelos\LoginModelo;
use DateTimeImmutable;

final class RepositorioLoginEloquent implements RepositorioLogin
{
    /**
     * Persiste una entidad Login en la base de datos mediante Eloquent.
     * Crea un nuevo registro si el ID es nulo, o actualiza si ya tiene ID.
     */
    public function guardar(Login $login): Login
    {
        if ($login->obtenerId() === null) {
            $modelo = LoginModelo::create([
                'usuario_id'            => $login->obtenerUsuarioId(),
                'nombre_sitio'          => $login->obtenerNombreSitio(),
                'url'                   => $login->obtenerUrl(),
                'usuario_login'         => $login->obtenerUsuarioLogin(),
                'contrasena_encriptada' => $login->obtenerContrasenaEncriptada(),
                'notas'                 => $login->obtenerNotas(),
            ]);
        } else {
            /** @var LoginModelo $modelo */
            $modelo = LoginModelo::findOrFail($login->obtenerId());
            $modelo->update([
                'nombre_sitio'          => $login->obtenerNombreSitio(),
                'url'                   => $login->obtenerUrl(),
                'usuario_login'         => $login->obtenerUsuarioLogin(),
                'contrasena_encriptada' => $login->obtenerContrasenaEncriptada(),
                'notas'                 => $login->obtenerNotas(),
            ]);
        }

        return $this->mapearAEntidad($modelo);
    }

    /**
     * Busca un login por su ID y lo mapea a la entidad de dominio.
     */
    public function buscarPorId(int $id): ?Login
    {
        /** @var LoginModelo|null $modelo */
        $modelo = LoginModelo::find($id);

        if ($modelo === null) {
            return null;
        }

        return $this->mapearAEntidad($modelo);
    }

    /**
     * Retorna todos los logins pertenecientes a un usuario.
     *
     * @return array<Login>
     */
    public function obtenerTodosPorUsuario(int $usuarioId): array
    {
        $modelos = LoginModelo::where('usuario_id', $usuarioId)
            ->orderBy('creado_en', 'desc')
            ->get();

        return $modelos->map(fn (LoginModelo $m) => $this->mapearAEntidad($m))->all();
    }

    /**
     * Elimina un login por su ID.
     */
    public function eliminar(int $id): bool
    {
        return LoginModelo::destroy($id) > 0;
    }

    /**
     * Mapea un modelo Eloquent LoginModelo a la entidad de dominio Login.
     */
    private function mapearAEntidad(LoginModelo $modelo): Login
    {
        return new Login(
            id:                   $modelo->id,
            usuarioId:            $modelo->usuario_id,
            nombreSitio:          $modelo->nombre_sitio,
            url:                  $modelo->url,
            usuarioLogin:         $modelo->usuario_login,
            contrasenaEncriptada: $modelo->contrasena_encriptada,
            notas:                $modelo->notas,
            creadoEn:             $modelo->creado_en ? DateTimeImmutable::createFromInterface($modelo->creado_en) : null,
            actualizadoEn:        $modelo->actualizado_en ? DateTimeImmutable::createFromInterface($modelo->actualizado_en) : null,
        );
    }
}
