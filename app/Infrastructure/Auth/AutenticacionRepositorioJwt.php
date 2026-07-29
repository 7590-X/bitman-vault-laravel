<?php

/**
 * Adaptador de infraestructura que implementa el puerto AutenticacionRepositorio
 * usando la librería php-open-source-saver/jwt-auth.
 *
 * Este adaptador encapsula completamente la dependencia de JWT en la capa de
 * infraestructura. Las capas de Dominio y Aplicación nunca importan la librería.
 */

namespace App\Infrastructure\Auth;

use App\Domain\Puertos\AutenticacionRepositorio;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

final class AutenticacionRepositorioJwt implements AutenticacionRepositorio
{
    /**
     * Intenta autenticar al usuario y emite un token JWT.
     *
     * NOTA: Para que JWTAuth::attempt() funcione correctamente con el guard `api`,
     * el campo de credenciales debe coincidir con la columna de la BD y la contraseña
     * debe ser verificable mediante el método `password` del modelo. Dado que usamos
     * sal+hash propio, el ManejadorIniciarSesion ya validó las credenciales antes de
     * llegar aquí. Aquí simplemente forzamos la emisión del token por correo electrónico.
     *
     * @param string $correo     Correo electrónico (ya verificado en el manejador).
     * @param string $contrasena Contraseña en texto plano (no se usa aquí, el manejador ya validó).
     */
    public function intentarLogin(string $correo, string $contrasena): ?string
    {
        // El manejador ya verificó la contraseña con sal+hash propio.
        // Aquí emitimos el token directamente buscando el modelo por correo.
        $modelo = \App\Infrastructure\Modelos\UsuarioModelo::where('correo_electronico', $correo)->first();

        if ($modelo === null) {
            return null;
        }

        /** @var string|false $token */
        $token = JWTAuth::fromUser($modelo);

        return $token ?: null;
    }

    /**
     * Invalida el token JWT del usuario actualmente autenticado.
     */
    public function cerrarSesion(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Refresca el token JWT activo y retorna uno nuevo.
     */
    public function refrescarToken(): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    /**
     * Retorna el ID del usuario autenticado actualmente, o null si no hay token válido.
     */
    public function obtenerIdUsuarioActual(): ?int
    {
        try {
            $modelo = JWTAuth::parseToken()->authenticate();
            return $modelo?->getKey();
        } catch (\Throwable) {
            return null;
        }
    }
}
