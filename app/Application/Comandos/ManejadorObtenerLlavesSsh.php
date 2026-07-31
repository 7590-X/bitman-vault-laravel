<?php

namespace App\Application\Comandos;

use App\Domain\Puertos\RepositorioLlaveSsh;

class ManejadorObtenerLlavesSsh
{
    private RepositorioLlaveSsh $repositorio;

    public function __construct(RepositorioLlaveSsh $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    /**
     * Retorna todas las llaves SSH de un usuario (Query).
     *
     * @param int $usuarioId
     * @return array
     */
    public function ejecutar(int $usuarioId): array
    {
        return $this->repositorio->obtenerPorUsuarioId($usuarioId);
    }
}
