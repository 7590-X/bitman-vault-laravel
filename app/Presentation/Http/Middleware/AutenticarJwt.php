<?php

/**
 * Middleware de autenticación JWT para rutas protegidas de la API.
 * Verifica la presencia y validez del token Bearer en el encabezado Authorization.
 * Responde con JSON 401 si el token falta, es inválido o ha expirado.
 */

namespace App\Presentation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class AutenticarJwt
{
    /**
     * Intercepta la petición y valida el token JWT.
     *
     * @param Request $solicitud Petición HTTP entrante.
     * @param Closure $siguiente Siguiente manejador de la cadena.
     */
    public function handle(Request $solicitud, Closure $siguiente): SymfonyResponse
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException) {
            return response()->json([
                'mensaje' => 'El token de acceso ha expirado.',
                'codigo'  => 'TOKEN_EXPIRADO',
            ], Response::HTTP_UNAUTHORIZED);
        } catch (TokenInvalidException) {
            return response()->json([
                'mensaje' => 'El token de acceso no es válido.',
                'codigo'  => 'TOKEN_INVALIDO',
            ], Response::HTTP_UNAUTHORIZED);
        } catch (JWTException) {
            return response()->json([
                'mensaje' => 'Se requiere un token de acceso.',
                'codigo'  => 'TOKEN_REQUERIDO',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $siguiente($solicitud);
    }
}
