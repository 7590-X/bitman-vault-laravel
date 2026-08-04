<?php

/**
 * Puerto del repositorio para la gestión de envíos de notas seguras.
 */

namespace App\Domain\Puertos;

use App\Domain\Entidades\EnvioNotaSegura;

interface RepositorioEnvioNotaSegura
{
    public function guardar(EnvioNotaSegura $nota): EnvioNotaSegura;

    /**
     * @return EnvioNotaSegura[]
     */
    public function buscarPorUsuarioId(int $usuarioId): array;

    public function buscarPorIdYUsuarioId(int $id, int $usuarioId): ?EnvioNotaSegura;

    public function buscarPorTokenAcceso(string $tokenAcceso): ?EnvioNotaSegura;

    public function marcarComoAperturadaYLimpiarContenido(int $id): void;

    public function eliminar(int $id, int $usuarioId): bool;
}
