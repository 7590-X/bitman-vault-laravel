<?php

/**
 * Controlador HTTP para los endpoints REST de gestión de Logins (bóveda).
 * Todos los endpoints requieren autenticación JWT previa.
 */

namespace App\Presentation\Http\Controllers;

use App\Application\Comandos\ComandoActualizarLogin;
use App\Application\Comandos\ComandoCrearLogin;
use App\Application\Comandos\ComandoEliminarLogin;
use App\Application\Comandos\ManejadorActualizarLogin;
use App\Application\Comandos\ManejadorCrearLogin;
use App\Application\Comandos\ManejadorEliminarLogin;
use App\Application\Comandos\ManejadorObtenerLogins;
use App\Domain\Entidades\Login;
use App\Presentation\Http\Requests\SolicitudActualizarLogin;
use App\Presentation\Http\Requests\SolicitudCrearLogin;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use RuntimeException;
use App\Presentation\Http\Responses\ApiResponse;
use App\Presentation\Http\Responses\HttpCode;

class ControladorLogin extends Controller
{
    public function __construct(
        private readonly ManejadorCrearLogin      $manejadorCrear,
        private readonly ManejadorObtenerLogins   $manejadorObtener,
        private readonly ManejadorActualizarLogin $manejadorActualizar,
        private readonly ManejadorEliminarLogin   $manejadorEliminar,
    ) {}

    /**
     * Retorna todos los logins pertenecientes al usuario autenticado.
     * GET /api/logins
     */
    public function index(): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();
        $logins = $this->manejadorObtener->manejar($usuarioId);

        $datos = array_map(fn(Login $login) => $this->transformarRespuesta($login), $logins);

        return response()->json([
            'datos' => $datos,
        ]);
    }

    /**
     * Registra un nuevo login en la bóveda del usuario autenticado.
     * POST /api/logins
     */
    public function store(SolicitudCrearLogin $solicitud): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();

        $comando = new ComandoCrearLogin(
            usuarioId: $usuarioId,
            nombreSitio: $solicitud->validated('nombre_sitio'),
            url: $solicitud->validated('url'),
            usuarioLogin: $solicitud->validated('usuario_login'),
            contrasenaEncriptada: $solicitud->validated('contrasena_encriptada'),
            notas: $solicitud->validated('notas'),
        );

        $login = $this->manejadorCrear->manejar($comando);

        return ApiResponse::exito('Login registrado exitosamente.', $this->transformarRespuesta($login), HttpCode::CREATED);
    }

    /**
     * Actualiza un login existente perteneciente al usuario autenticado.
     * PUT /api/logins/{id}
     */
    public function update(SolicitudActualizarLogin $solicitud, int $id): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();

        $comando = new ComandoActualizarLogin(
            id: $id,
            usuarioId: $usuarioId,
            nombreSitio: $solicitud->validated('nombre_sitio'),
            url: $solicitud->validated('url'),
            usuarioLogin: $solicitud->validated('usuario_login'),
            contrasenaEncriptada: $solicitud->validated('contrasena_encriptada'),
            notas: $solicitud->validated('notas'),
        );

        try {
            $login = $this->manejadorActualizar->manejar($comando);
        } catch (RuntimeException $ex) {
            $codigoNum = $ex->getCode() >= 400 && $ex->getCode() < 600 ? $ex->getCode() : 400;
            $codigoEnum = HttpCode::tryFrom($codigoNum) ?? HttpCode::BAD_REQUEST;
            return ApiResponse::error($ex->getMessage(), $codigoEnum);
        }

        return ApiResponse::exito('Login actualizado exitosamente.', $this->transformarRespuesta($login));
    }

    /**
     * Elimina un login perteneciente al usuario autenticado.
     * DELETE /api/logins/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $usuarioId = (int) auth('api')->id();

        $comando = new ComandoEliminarLogin(
            id: $id,
            usuarioId: $usuarioId,
        );

        try {
            $this->manejadorEliminar->manejar($comando);
        } catch (RuntimeException $ex) {
            $codigoNum = $ex->getCode() >= 400 && $ex->getCode() < 600 ? $ex->getCode() : 400;
            $codigoEnum = HttpCode::tryFrom($codigoNum) ?? HttpCode::BAD_REQUEST;
            return ApiResponse::error($ex->getMessage(), $codigoEnum);
        }

        return ApiResponse::exito('Login eliminado exitosamente.');
    }

    /**
     * Mapea una entidad Login a un arreglo asociativo para la respuesta JSON.
     */
    private function transformarRespuesta(Login $login): array
    {
        return [
            'id'                    => $login->obtenerId(),
            'usuario_id'            => $login->obtenerUsuarioId(),
            'nombre_sitio'          => $login->obtenerNombreSitio(),
            'url'                   => $login->obtenerUrl(),
            'usuario_login'         => $login->obtenerUsuarioLogin(),
            'contrasena_encriptada' => $login->obtenerContrasenaEncriptada(),
            'notas'                 => $login->obtenerNotas(),
            'creado_en'             => $login->obtenerCreadoEn()?->format('Y-m-d H:i:s'),
            'actualizado_en'        => $login->obtenerActualizadoEn()?->format('Y-m-d H:i:s'),
        ];
    }
}
