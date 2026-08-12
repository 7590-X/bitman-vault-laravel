<?php

/**
 * Controlador HTTP para los endpoints de autenticación JWT.
 *
 * Endpoints:
 *   POST   /api/auth/login    → Emite un token JWT.
 *   POST   /api/auth/logout   → Invalida el token activo.
 *   POST   /api/auth/refresh  → Renueva el token (retorna uno nuevo).
 *   GET    /api/auth/me       → Retorna los datos del usuario autenticado.
 */

namespace App\Presentation\Http\Controllers;

use App\Application\Comandos\ComandoIniciarSesion;
use App\Application\Comandos\ManejadorIniciarSesion;
use App\Domain\Puertos\AutenticacionRepositorio;
use App\Infrastructure\Modelos\UsuarioModelo;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use RuntimeException;
use App\Presentation\Http\Requests\SolicitudIniciarSesion;
use App\Presentation\Http\Responses\ApiResponse;
use App\Presentation\Http\Responses\HttpCode;

class ControladorAutenticacion extends Controller
{
    /**
     * Inyecta el manejador de login y el repositorio de autenticación.
     */
    public function __construct(
        private readonly ManejadorIniciarSesion   $manejador,
        private readonly AutenticacionRepositorio $autenticacion,
    ) {}

    /**
     * Procesa las credenciales y emite un token JWT si son válidas.
     *
     * POST /api/auth/login
     */
    public function login(SolicitudIniciarSesion $solicitud): JsonResponse
    {
        $comando = new ComandoIniciarSesion(
            correoElectronico: $solicitud->validated('correo_electronico'),
            contrasena: $solicitud->validated('contrasena'),
        );

        try {
            $token = $this->manejador->manejar($comando);
        } catch (RuntimeException $excepcion) {
            return ApiResponse::error($excepcion->getMessage(), HttpCode::UNAUTHORIZED);
        }

        return $this->respuestaToken($token);
    }

    /**
     * Invalida el token JWT activo del usuario.
     *
     * POST /api/auth/logout
     */
    public function logout(): JsonResponse
    {
        $this->autenticacion->cerrarSesion();

        return ApiResponse::exito('Sesión cerrada correctamente.');
    }

    /**
     * Emite un nuevo token JWT y descarta el actual.
     *
     * POST /api/auth/refresh
     */
    public function refresh(): JsonResponse
    {
        try {
            $nuevoToken = $this->autenticacion->refrescarToken();
        } catch (RuntimeException) {
            return ApiResponse::error('No se pudo renovar el token.', HttpCode::UNAUTHORIZED);
        }

        return $this->respuestaToken($nuevoToken);
    }

    /**
     * Retorna los datos del usuario autenticado actualmente.
     *
     * GET /api/auth/me
     */
    public function me(): JsonResponse
    {
        /** @var UsuarioModelo $usuario */
        $usuario = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'id'                 => $usuario->id,
            'nombre_completo'    => $usuario->nombre_completo,
            'correo_electronico' => $usuario->correo_electronico,
            'estado'             => $usuario->estado?->value,
            'creado_en'          => $usuario->creado_en?->toISOString(),
        ]);
    }

    /**
     * Construye la respuesta JSON estándar con el token JWT.
     */
    private function respuestaToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => config('jwt.ttl') * 60,
        ]);
    }
}
