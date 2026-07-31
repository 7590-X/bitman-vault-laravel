<?php

namespace App\Domain\Puertos;

interface RepositorioLlaveSsh
{
    /**
     * Obtiene todas las llaves SSH de un usuario específico.
     *
     * @param int $usuarioId
     * @return array
     */
    public function obtenerPorUsuarioId(int $usuarioId): array;

    /**
     * Guarda una nueva llave SSH.
     *
     * @param array $datos
     * @return object
     */
    public function guardar(array $datos): object;

    /**
     * Elimina una llave SSH dado su ID y el ID del usuario propietario.
     *
     * @param int $id
     * @param int $usuarioId
     * @return bool
     */
    public function eliminar(int $id, int $usuarioId): bool;
}
