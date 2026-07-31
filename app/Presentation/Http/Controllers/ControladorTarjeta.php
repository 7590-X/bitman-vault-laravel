<?php

/**
 * Controlador HTTP para los endpoints REST de gestión de Tarjetas.
 * Todos los endpoints requieren autenticación JWT previa.
 */

namespace App\Presentation\Http\Controllers;

use App\Application\Comandos\ComandoActualizarTarjeta;
use App\Application\Comandos\ComandoCrearTarjeta;
use App\Application\Comandos\ComandoEliminarTarjeta;
use App\Application\Comandos\ManejadorActualizarTarjeta;
use App\Application\Comandos\ManejadorCrearTarjeta;
use App\Application\Comandos\ManejadorEliminarTarjeta;
use App\Application\Comandos\ManejadorObtenerTarjetas;
use App\Domain\Entidades\Tarjeta;
use App\Presentation\Http\Requests\SolicitudActualizarTarjeta;
use App\Presentation\Http\Requests\SolicitudCrearTarjeta;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use RuntimeException;

class ControladorTarjeta extends Controller
{
    public function __construct(
        private readonly ManejadorCrearTarjeta      $manejadorCrear,
        private readonly ManejadorObtenerTarjetas   $manejadorObtener,
        private readonly ManejadorActualizarTarjeta $manejadorActualizar,
        private readonly ManejadorEliminarTarjeta   $manejadorEliminar,
    ) {}

    /**
     * Retorna todas las tarjetas pertenecientes al usuario autenticado.
     * GET /api/tarjetas
     */
    public function index(): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();
        $tarjetas = $this->manejadorObtener->manejar($usuarioId);

        $datos = array_map(fn (Tarjeta $tarjeta) => $this->transformarRespuesta($tarjeta), $tarjetas);

        return response()->json([
            'datos' => $datos,
        ]);
    }

    /**
     * Registra una nueva tarjeta para el usuario autenticado.
     * POST /api/tarjetas
     */
    public function store(SolicitudCrearTarjeta $solicitud): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();

        $comando = new ComandoCrearTarjeta(
            usuarioId:        $usuarioId,
            tipoTarjetaId:    $solicitud->validated('tipo_tarjeta_id'),
            alias:            $solicitud->validated('alias'),
            numeroEncriptado: $solicitud->validated('numero_encriptado'),
            ultimos4Digitos:  $solicitud->validated('ultimos_4_digitos'),
            nombreTitular:    $solicitud->validated('nombre_titular'),
            fechaExpiracion:  new DateTimeImmutable($solicitud->validated('fecha_expiracion')),
            cvvEncriptado:    $solicitud->validated('cvv_encriptado'),
            bancoEmisor:      $solicitud->validated('banco_emisor'),
        );

        $tarjeta = $this->manejadorCrear->manejar($comando);

        return response()->json([
            'mensaje' => 'Tarjeta registrada exitosamente.',
            'datos'   => $this->transformarRespuesta($tarjeta),
        ], Response::HTTP_CREATED);
    }

    /**
     * Actualiza una tarjeta existente perteneciente al usuario autenticado.
     * PUT /api/tarjetas/{id}
     */
    public function update(SolicitudActualizarTarjeta $solicitud, int $id): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();

        $comando = new ComandoActualizarTarjeta(
            id:               $id,
            usuarioId:        $usuarioId,
            tipoTarjetaId:    $solicitud->validated('tipo_tarjeta_id'),
            alias:            $solicitud->validated('alias'),
            numeroEncriptado: $solicitud->validated('numero_encriptado'),
            ultimos4Digitos:  $solicitud->validated('ultimos_4_digitos'),
            nombreTitular:    $solicitud->validated('nombre_titular'),
            fechaExpiracion:  new DateTimeImmutable($solicitud->validated('fecha_expiracion')),
            cvvEncriptado:    $solicitud->validated('cvv_encriptado'),
            bancoEmisor:      $solicitud->validated('banco_emisor'),
        );

        try {
            $tarjeta = $this->manejadorActualizar->manejar($comando);
        } catch (RuntimeException $ex) {
            $codigo = $ex->getCode() >= 400 && $ex->getCode() < 600 ? $ex->getCode() : Response::HTTP_BAD_REQUEST;
            return response()->json(['mensaje' => $ex->getMessage()], $codigo);
        }

        return response()->json([
            'mensaje' => 'Tarjeta actualizada exitosamente.',
            'datos'   => $this->transformarRespuesta($tarjeta),
        ]);
    }

    /**
     * Elimina una tarjeta perteneciente al usuario autenticado.
     * DELETE /api/tarjetas/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();

        $comando = new ComandoEliminarTarjeta(
            id:        $id,
            usuarioId: $usuarioId,
        );

        try {
            $this->manejadorEliminar->manejar($comando);
        } catch (RuntimeException $ex) {
            $codigo = $ex->getCode() >= 400 && $ex->getCode() < 600 ? $ex->getCode() : Response::HTTP_BAD_REQUEST;
            return response()->json(['mensaje' => $ex->getMessage()], $codigo);
        }

        return response()->json([
            'mensaje' => 'Tarjeta eliminada exitosamente.',
        ]);
    }

    /**
     * Mapea una entidad Tarjeta a un arreglo asociativo para la respuesta JSON.
     */
    private function transformarRespuesta(Tarjeta $tarjeta): array
    {
        return [
            'id'                => $tarjeta->obtenerId(),
            'usuario_id'        => $tarjeta->obtenerUsuarioId(),
            'tipo_tarjeta_id'   => $tarjeta->obtenerTipoTarjetaId(),
            'alias'             => $tarjeta->obtenerAlias(),
            'numero_encriptado' => $tarjeta->obtenerNumeroEncriptado(),
            'ultimos_4_digitos' => $tarjeta->obtenerUltimos4Digitos(),
            'nombre_titular'    => $tarjeta->obtenerNombreTitular(),
            'fecha_expiracion'  => $tarjeta->obtenerFechaExpiracion()->format('Y-m-d'),
            'cvv_encriptado'    => $tarjeta->obtenerCvvEncriptado(),
            'banco_emisor'      => $tarjeta->obtenerBancoEmisor(),
            'creado_en'         => $tarjeta->obtenerCreadoEn()?->format('Y-m-d H:i:s'),
        ];
    }
}
