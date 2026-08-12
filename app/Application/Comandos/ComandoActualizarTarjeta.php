<?php

/**
 * DTO que encapsula los datos requeridos para actualizar una tarjeta existente.
 */

namespace App\Application\Comandos;

use DateTimeImmutable;

final readonly class ComandoActualizarTarjeta
{
    public function __construct(
        public int               $id,
        public int               $usuarioId,
        public int               $tipoTarjetaId,
        public string            $alias,
        public string            $numeroEncriptado,
        public string            $ultimos4Digitos,
        public string            $nombreTitular,
        public DateTimeImmutable $fechaExpiracion,
        public string            $cvvEncriptado,
        public ?string           $bancoEmisor,
    ) {}
}
