<?php

namespace App\Application\Comandos;

use App\Domain\Puertos\RepositorioLlaveSsh;
use Illuminate\Support\Facades\Log;

class ManejadorEliminarLlaveSsh
{
    private RepositorioLlaveSsh $repositorio;

    public function __construct(RepositorioLlaveSsh $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    /**
     * Ejecuta la eliminación de una llave SSH verificando su pertenencia.
     *
     * @param ComandoEliminarLlaveSsh $comando
     * @return bool
     */
    public function ejecutar(ComandoEliminarLlaveSsh $comando): bool
    {
        try {
            return $this->repositorio->eliminar($comando->id, $comando->usuarioId);
        } catch (\Exception $e) {
            Log::error('Error al eliminar llave SSH: ' . $e->getMessage());
            throw $e;
        }
    }
}
