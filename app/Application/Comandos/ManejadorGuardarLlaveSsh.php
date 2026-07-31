<?php

namespace App\Application\Comandos;

use App\Domain\Puertos\RepositorioLlaveSsh;
use Illuminate\Support\Facades\Log;

class ManejadorGuardarLlaveSsh
{
    private RepositorioLlaveSsh $repositorio;

    public function __construct(RepositorioLlaveSsh $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    /**
     * Ejecuta el guardado de la llave SSH.
     *
     * @param ComandoGuardarLlaveSsh $comando
     * @return object
     */
    public function ejecutar(ComandoGuardarLlaveSsh $comando): object
    {
        try {
            $datos = [
                'usuario_id'               => $comando->usuarioId,
                'nombre'                   => $comando->nombre,
                'llave_privada_encriptada' => $comando->llavePrivadaEncriptada,
                'llave_publica'            => $comando->llavePublica,
                'frase_paso_encriptada'    => $comando->frasePasoEncriptada,
            ];

            return $this->repositorio->guardar($datos);
        } catch (\Exception $e) {
            Log::error('Error al guardar llave SSH: ' . $e->getMessage());
            throw $e;
        }
    }
}
