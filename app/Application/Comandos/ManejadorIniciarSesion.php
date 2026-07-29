<?php

/**
 * Manejador del comando para iniciar sesión en la bóveda.
 *
 * Implementa la lógica de negocio del caso de uso de autenticación:
 *   1. Verifica que el usuario existe por correo.
 *   2. Aplica la sal propia del sistema para rehashear la contraseña entrante.
 *   3. Compara con el hash almacenado.
 *   4. Si es válido, delega la emisión del JWT al repositorio de autenticación.
 *
 * IMPORTANTE: El sistema usa sal+hash propio (bin2hex + PASSWORD_BCRYPT), por lo que
 * no se puede usar Hash::check() de Laravel de forma directa. Se rehashea con la sal
 * del usuario y se usa password_verify() estándar de PHP.
 */

namespace App\Application\Comandos;

use App\Domain\Enums\EstadoUsuario;
use App\Domain\Puertos\AutenticacionRepositorio;
use App\Domain\Puertos\RepositorioUsuario;
use RuntimeException;

final class ManejadorIniciarSesion
{
    /**
     * Inyecta los repositorios necesarios para el caso de uso de login.
     */
    public function __construct(
        private readonly RepositorioUsuario      $repositorioUsuario,
        private readonly AutenticacionRepositorio $autenticacion,
    ) {}

    /**
     * Ejecuta el caso de uso de inicio de sesión.
     *
     * @return string Token JWT emitido al usuario autenticado.
     * @throws RuntimeException Si las credenciales son inválidas o la cuenta está inactiva.
     */
    public function manejar(ComandoIniciarSesion $comando): string
    {
        // 1. Buscar usuario por correo (incluye sal y hash necesarios para verificar)
        $usuario = $this->repositorioUsuario->buscarPorCorreo($comando->correoElectronico);

        if ($usuario === null) {
            // Mensaje genérico — no revelar si el correo existe o no
            throw new RuntimeException('Credenciales inválidas.');
        }

        // 2. Verificar que la cuenta esté activa
        if ($usuario->obtenerEstado() !== EstadoUsuario::Activo) {
            throw new RuntimeException('Esta cuenta está suspendida o ha sido eliminada.');
        }

        // 3. Rehashear la contraseña entrante con la sal del usuario y comparar
        $esValida = password_verify(
            $usuario->obtenerSalContrasena() . $comando->contrasena,
            $usuario->obtenerHashContrasena(),
        );

        if (!$esValida) {
            throw new RuntimeException('Credenciales inválidas.');
        }

        // 4. Delegar la emisión del JWT al adaptador de infraestructura
        $token = $this->autenticacion->intentarLogin(
            $comando->correoElectronico,
            $comando->contrasena,
        );

        if ($token === null) {
            // Seguridad: no debería ocurrir si pasó el paso 3, pero se maneja
            throw new RuntimeException('No se pudo generar el token de acceso.');
        }

        return $token;
    }
}
