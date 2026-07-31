<?php

/**
 * Manejador del comando para eliminar una tarjeta existente.
 */

namespace App\Application\Comandos;

use App\Domain\Puertos\RepositorioTarjeta;
use RuntimeException;

final class ManejadorEliminarTarjeta
{
    public function __construct(
        private readonly RepositorioTarjeta $repositorioTarjeta,
    ) {}

    /**
     * Ejecuta el caso de uso de eliminación de tarjeta.
     *
     * @throws RuntimeException Si la tarjeta no existe o no pertenece al usuario.
     */
    public function manejar(ComandoEliminarTarjeta $comando): void
    {
        $tarjetaExistente = $this->repositorioTarjeta->buscarPorId($comando->id);

        if ($tarjetaExistente === null) {
            throw new RuntimeException('El registro de la tarjeta no existe.', 404);
        }

        if ($tarjetaExistente->obtenerUsuarioId() !== $comando->usuarioId) {
            throw new RuntimeException('No tienes permiso para eliminar este recurso.', 403);
        }

        $this->repositorioTarjeta->eliminar($comando->id);
    }
}
