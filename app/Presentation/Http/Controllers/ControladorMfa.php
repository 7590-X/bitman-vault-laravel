<?php

/**
 * Controlador REST para la gestión del registro y verificación de MFA.
 */

namespace App\Presentation\Http\Controllers;

use App\Application\Comandos\ComandoConfirmarMfa;
use App\Application\Comandos\ComandoRegistrarMfa;
use App\Application\Comandos\ManejadorConfirmarMfa;
use App\Application\Comandos\ManejadorDesactivarMfa;
use App\Application\Comandos\ManejadorObtenerEstadoMfa;
use App\Application\Comandos\ManejadorRegistrarMfa;
use App\Domain\Enums\MetodoEnvioMfa;
use App\Presentation\Http\Requests\SolicitudConfirmarMfa;
use App\Presentation\Http\Requests\SolicitudRegistrarMfa;
use App\Presentation\Http\Responses\ApiResponse;
use App\Presentation\Http\Responses\HttpCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use RuntimeException;

class ControladorMfa extends Controller
{
    public function __construct(
        private readonly ManejadorRegistrarMfa     $manejadorRegistrar,
        private readonly ManejadorConfirmarMfa     $manejadorConfirmar,
        private readonly ManejadorObtenerEstadoMfa $manejadorObtenerEstado,
        private readonly ManejadorDesactivarMfa    $manejadorDesactivar,
    ) {}

    /**
     * Inicia el proceso de registro de MFA generando el secreto y enviando el código.
     * POST /api/mfa/registrar
     */
    public function registrar(SolicitudRegistrarMfa $solicitud): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();
        $metodoEnvioStr = $solicitud->validated('metodo_envio') ?? 'correo';
        $metodoEnvio = MetodoEnvioMfa::from($metodoEnvioStr);

        $comando = new ComandoRegistrarMfa(
            usuarioId:   $usuarioId,
            metodoEnvio: $metodoEnvio,
        );

        try {
            $resultado = $this->manejadorRegistrar->manejar($comando);
            return ApiResponse::exito('Registro de MFA iniciado. Código enviado.', $resultado, HttpCode::OK);
        } catch (RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), HttpCode::BAD_REQUEST);
        }
    }

    /**
     * Confirma el código de 6 dígitos enviado al usuario y activa el MFA.
     * POST /api/mfa/confirmar
     */
    public function confirmar(SolicitudConfirmarMfa $solicitud): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();

        $comando = new ComandoConfirmarMfa(
            usuarioId: $usuarioId,
            codigo:    $solicitud->validated('codigo'),
        );

        try {
            $resultado = $this->manejadorConfirmar->manejar($comando);
            return ApiResponse::exito('MFA confirmado y activado correctamente.', $resultado, HttpCode::OK);
        } catch (RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), HttpCode::BAD_REQUEST);
        }
    }

    /**
     * Retorna el estado actual de la configuración MFA del usuario autenticado.
     * GET /api/mfa/estado
     */
    public function estado(): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();
        $estado = $this->manejadorObtenerEstado->manejar($usuarioId);

        return response()->json([
            'datos' => $estado,
        ]);
    }

    /**
     * Desactiva el MFA para el usuario autenticado.
     * POST /api/mfa/desactivar
     */
    public function desactivar(): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();
        $resultado = $this->manejadorDesactivar->manejar($usuarioId);

        return ApiResponse::exito('MFA desactivado correctamente.', $resultado, HttpCode::OK);
    }
}
