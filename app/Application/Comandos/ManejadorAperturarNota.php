<?php

/**
 * Manejador del caso de uso para aperturar y leer el contenido de una nota segura (Self-Destruct).
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\EnvioNotaSegura;
use App\Domain\Puertos\RepositorioEnvioNotaSegura;
use RuntimeException;

final class ManejadorAperturarNota
{
    public function __construct(
        private readonly RepositorioEnvioNotaSegura $repositorio,
    ) {}

    public function manejar(ComandoAperturarNota $comando): array
    {
        $nota = $this->repositorio->buscarPorTokenAcceso($comando->tokenAcceso);

        if (!$nota) {
            throw new RuntimeException('La nota no existe o el enlace no es válido.', 404);
        }

        if ($nota->estaAperturada()) {
            throw new RuntimeException('Esta nota ya fue aperturada previamente y su contenido ha sido eliminado.', 410);
        }

        if ($nota->estaExpirada()) {
            throw new RuntimeException('Esta nota ha expirado y ya no se encuentra disponible.', 410);
        }

        if (trim($nota->obtenerCodigoApertura()) !== trim($comando->codigoApertura)) {
            throw new RuntimeException('El código de apertura ingresado es incorrecto.', 400);
        }

        $contenido = $nota->obtenerContenidoEncriptado();
        $duracionVisualizacion = $nota->obtenerDuracionVisualizacionSegundos();

        // Autodestrucción inmediata en BD
        $this->repositorio->marcarComoAperturadaYLimpiarContenido($nota->obtenerId());

        return [
            'titulo'                        => $nota->obtenerTitulo(),
            'contenido'                     => $contenido,
            'duracion_visualizacion_segundos' => $duracionVisualizacion,
        ];
    }
}
