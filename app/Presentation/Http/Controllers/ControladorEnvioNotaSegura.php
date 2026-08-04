<?php

/**
 * Controlador REST para el módulo Enviar Notas Seguras.
 * Implementa la seguridad CIA y la regla de privacidad para no retornar el texto al emisor.
 */

namespace App\Presentation\Http\Controllers;

use App\Application\Comandos\ComandoAperturarNota;
use App\Application\Comandos\ComandoCrearEnvioNotaSegura;
use App\Application\Comandos\ManejadorAperturarNota;
use App\Application\Comandos\ManejadorCrearEnvioNotaSegura;
use App\Application\Comandos\ManejadorEliminarEnvioNota;
use App\Application\Comandos\ManejadorObtenerEnviosNotas;
use App\Domain\Entidades\EnvioNotaSegura;
use App\Presentation\Http\Requests\SolicitudCrearEnvioNotaSegura;
use App\Presentation\Http\Responses\ApiResponse;
use App\Presentation\Http\Responses\HttpCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RuntimeException;

class ControladorEnvioNotaSegura extends Controller
{
    public function __construct(
        private readonly ManejadorCrearEnvioNotaSegura $manejadorCrear,
        private readonly ManejadorObtenerEnviosNotas  $manejadorObtener,
        private readonly ManejadorAperturarNota       $manejadorAperturar,
        private readonly ManejadorEliminarEnvioNota   $manejadorEliminar,
    ) {}

    /**
     * Retorna todas las notas enviadas por el usuario autenticado.
     * GET /api/enviar-notas
     */
    public function index(): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();
        $notas = $this->manejadorObtener->manejar($usuarioId);

        $datos = array_map(fn(EnvioNotaSegura $n) => $this->transformarRespuestaEmisor($n), $notas);

        return response()->json([
            'datos' => $datos,
        ]);
    }

    /**
     * Crea un nuevo envío de nota segura.
     * POST /api/enviar-notas
     */
    public function store(SolicitudCrearEnvioNotaSegura $solicitud): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();

        $comando = new ComandoCrearEnvioNotaSegura(
            usuarioId:                     $usuarioId,
            titulo:                        $solicitud->validated('titulo'),
            correoDestino:                 $solicitud->validated('correo_destino'),
            textoNota:                     $solicitud->validated('texto_nota'),
            codigoApertura:                $solicitud->validated('codigo_apertura'),
            minutosExpiracion:             (int) ($solicitud->validated('minutos_expiracion') ?? 30),
            duracionVisualizacionSegundos: (int) ($solicitud->validated('duracion_visualizacion_segundos') ?? 30),
        );

        $nota = $this->manejadorCrear->manejar($comando);

        return ApiResponse::exito(
            'Nota segura registrada y lista para enviar.',
            $this->transformarRespuestaEmisor($nota),
            HttpCode::CREATED
        );
    }

    /**
     * Elimina / revoca una nota enviada.
     * DELETE /api/enviar-notas/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();

        try {
            $this->manejadorEliminar->manejar($id, $usuarioId);
        } catch (RuntimeException $ex) {
            return ApiResponse::error($ex->getMessage(), HttpCode::NOT_FOUND);
        }

        return ApiResponse::exito('Nota segura eliminada correctamente.');
    }

    /**
     * Endpoint para aperturar y leer una nota única por token de acceso y código.
     * POST /api/enviar-notas/aperturar/{token}
     */
    public function aperturar(Request $solicitud, string $token): JsonResponse
    {
        $codigoApertura = (string) $solicitud->input('codigo_apertura', '');

        $comando = new ComandoAperturarNota(
            tokenAcceso: $token,
            codigoApertura: $codigoApertura,
        );

        try {
            $resultado = $this->manejadorAperturar->manejar($comando);
            return ApiResponse::exito('Nota aperturada correctamente.', $resultado, HttpCode::OK);
        } catch (RuntimeException $ex) {
            $code = $ex->getCode() >= 400 && $ex->getCode() < 600 ? $ex->getCode() : 400;
            return ApiResponse::error($ex->getMessage(), HttpCode::tryFrom($code) ?? HttpCode::BAD_REQUEST);
        }
    }

    /**
     * Mapea la entidad EnvioNotaSegura para la respuesta enviada al usuario emisor.
     * REGLA 6: El contenido cifrado NUNCA se incluye en la respuesta del emisor/creador.
     */
    private function transformarRespuestaEmisor(EnvioNotaSegura $nota): array
    {
        return [
            'id'                              => $nota->obtenerId(),
            'usuario_id'                      => $nota->obtenerUsuarioId(),
            'titulo'                          => $nota->obtenerTitulo(),
            'correo_destino'                  => $nota->obtenerCorreoDestino(),
            'codigo_apertura'                 => $nota->obtenerCodigoApertura(),
            'token_acceso'                    => $nota->obtenerTokenAcceso(),
            'url_acceso'                      => url("/#/notas/aperturar/{$nota->obtenerTokenAcceso()}"),
            'estado'                          => $nota->obtenerEstado()->value,
            'estado_etiqueta'                 => $nota->obtenerEstado()->etiqueta(),
            'duracion_visualizacion_segundos' => $nota->obtenerDuracionVisualizacionSegundos(),
            'expira_en'                       => $nota->obtenerExpiraEn()->format('Y-m-d H:i:s'),
            'aperturado_en'                   => $nota->obtenerAperturadoEn()?->format('Y-m-d H:i:s'),
            'creado_en'                       => $nota->obtenerCreadoEn()?->format('Y-m-d H:i:s'),
            'actualizado_en'                  => $nota->obtenerActualizadoEn()?->format('Y-m-d H:i:s'),
        ];
    }
}
