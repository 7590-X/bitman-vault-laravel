<?php

/**
 * Puerto (interfaz) del servicio de autenticación JWT.
 * Define el contrato que debe cumplir cualquier implementación de infraestructura.
 * La capa de dominio nunca depende de la librería JWT directamente.
 */

namespace App\Domain\Puertos;

interface AutenticacionRepositorio
{
    /**
     * Intenta autenticar al usuario con las credenciales dadas.
     * Retorna el token JWT si las credenciales son válidas, o null si no.
     *
     * @param string $correo     Correo electrónico del usuario.
     * @param string $contrasena Contraseña en texto plano (se verificará contra sal+hash).
     */
    public function intentarLogin(string $correo, string $contrasena): ?string;

    /**
     * Invalida el token JWT activo del usuario autenticado.
     */
    public function cerrarSesion(): void;

    /**
     * Refresca el token JWT activo y retorna uno nuevo.
     * El token anterior queda invalidado.
     */
    public function refrescarToken(): string;

    /**
     * Retorna el ID del usuario autenticado actualmente.
     * Retorna null si no hay usuario autenticado.
     */
    public function obtenerIdUsuarioActual(): ?int;
}
