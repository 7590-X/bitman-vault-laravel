<?php

/**
 * Puerto (interfaz) del repositorio de tarjetas.
 * Define el contrato de persistencia para la entidad de dominio Tarjeta.
 */

namespace App\Domain\Puertos;

use App\Domain\Entidades\Tarjeta;

interface RepositorioTarjeta
{
    /**
     * Persiste una tarjeta (creación o actualización).
     * Retorna la instancia de Tarjeta con su ID asignado en caso de creación.
     */
    public function guardar(Tarjeta $tarjeta): Tarjeta;

    /**
     * Busca una tarjeta por su identificador único.
     * Retorna null si no se encuentra.
     */
    public function buscarPorId(int $id): ?Tarjeta;

    /**
     * Retorna todas las tarjetas pertenecientes al usuario especificado.
     *
     * @param int $usuarioId ID del usuario propietario.
     * @return array<Tarjeta> Lista de entidades de tarjeta.
     */
    public function obtenerTodasPorUsuario(int $usuarioId): array;

    /**
     * Elimina una tarjeta por su ID.
     * Retorna true si se eliminó correctamente, false en caso contrario.
     */
    public function eliminar(int $id): bool;
}
