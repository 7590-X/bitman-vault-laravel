<?php

/**
 * Comando DTO para la creación de un Envío de Nota Segura.
 */

namespace App\Application\Comandos;

final class ComandoCrearEnvioNotaSegura
{
    public function __construct(
        public readonly int $usuarioId,
        public readonly string $titulo,
        public readonly string $correoDestino,
        public readonly string $textoNota,
        public readonly ?string $codigoApertura = null,
        public readonly int $minutosExpiracion = 30,
        public readonly int $duracionVisualizacionSegundos = 30,
    ) {}
}
