<?php

namespace App\Infrastructure\Repositorios;

use App\Domain\Puertos\RepositorioLlaveSsh;
use App\Infrastructure\Modelos\LlaveSshModelo;

class RepositorioLlaveSshEloquent implements RepositorioLlaveSsh
{
    public function obtenerPorUsuarioId(int $usuarioId): array
    {
        return LlaveSshModelo::where('usuario_id', $usuarioId)
            ->orderBy('creado_en', 'desc')
            ->get()
            ->toArray();
    }

    public function guardar(array $datos): object
    {
        return LlaveSshModelo::create($datos);
    }

    public function eliminar(int $id, int $usuarioId): bool
    {
        $eliminados = LlaveSshModelo::where('id', $id)
            ->where('usuario_id', $usuarioId)
            ->delete();

        return $eliminados > 0;
    }
}
