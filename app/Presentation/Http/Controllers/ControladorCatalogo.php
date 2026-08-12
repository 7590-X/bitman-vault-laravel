<?php

/**
 * Controlador HTTP para proveer los distintos catálogos del sistema al frontend.
 * Agrupa tanto catálogos basados en base de datos como enumeradores del dominio.
 */

namespace App\Presentation\Http\Controllers;

use App\Application\Comandos\ManejadorObtenerTiposTarjeta;
use App\Domain\Entidades\TipoTarjeta;
use App\Domain\Enums\EstadoInterruptor;
use App\Domain\Enums\EstadoNotaCompartida;
use App\Domain\Enums\EstadoUsuario;
use App\Domain\Enums\MetodoEnvioMfa;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Presentation\Http\Responses\ApiResponse;

class ControladorCatalogo extends Controller
{
    public function __construct(
        private readonly ManejadorObtenerTiposTarjeta $manejadorTiposTarjeta
    ) {}

    /**
     * Retorna el catálogo de tipos de tarjeta (desde la Base de Datos).
     * GET /api/catalogos/tipos-tarjeta
     */
    public function obtenerTiposTarjeta(): JsonResponse
    {
        $tipos = $this->manejadorTiposTarjeta->manejar();

        $datos = array_map(fn(TipoTarjeta $tipo) => [
            'id'     => $tipo->obtenerIdTipo(),
            'nombre' => $tipo->obtenerNombre(),
        ], $tipos);

        return response()->json(['datos' => $datos]);
    }
}
