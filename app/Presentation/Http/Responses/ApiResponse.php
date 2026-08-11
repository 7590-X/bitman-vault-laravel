<?php

namespace App\Presentation\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Construye y unifica la estructura base de la respuesta JSON (DRY).
     */
    private static function build(string $mensaje, HttpCode $codigo, mixed $payload = null): JsonResponse
    {
        return response()->json([
            'codigo'    => $codigo->value,
            'mensaje'   => $mensaje,
            'payload'   => $payload,
            'datos'     => $payload,
            'timestamp' => now()->toIso8601String(),
        ], $codigo->value);
    }

    /**
     * Retorna una respuesta JSON estandarizada de éxito.
     */
    public static function exito(string $mensaje, mixed $payload = null, HttpCode $codigo = HttpCode::OK): JsonResponse
    {
        return self::build($mensaje, $codigo, $payload);
    }

    /**
     * Retorna una respuesta JSON estandarizada de error.
     */
    public static function error(string $mensaje, HttpCode $codigo = HttpCode::BAD_REQUEST, mixed $payload = null): JsonResponse
    {
        return self::build($mensaje, $codigo, $payload);
    }
}
