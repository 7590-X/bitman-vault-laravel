<?php

/**
 * Manejador del comando para actualizar una tarjeta existente.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\Tarjeta;
use App\Domain\Puertos\RepositorioTarjeta;
use RuntimeException;

final class ManejadorActualizarTarjeta
{
    public function __construct(
        private readonly RepositorioTarjeta $repositorioTarjeta,
    ) {}

    /**
     * Ejecuta el caso de uso de actualización de tarjeta.
     *
     * @throws RuntimeException Si la tarjeta no existe o no pertenece al usuario.
     */
    public function manejar(ComandoActualizarTarjeta $comando): Tarjeta
    {
        $tarjetaExistente = $this->repositorioTarjeta->buscarPorId($comando->id);

        if ($tarjetaExistente === null) {
            throw new RuntimeException('El registro de la tarjeta no existe.', 404);
        }

        if ($tarjetaExistente->obtenerUsuarioId() !== $comando->usuarioId) {
            throw new RuntimeException('No tienes permiso para modificar este recurso.', 403);
        }

        $tarjetaActualizada = new Tarjeta(
            id:               $comando->id,
            usuarioId:        $comando->usuarioId,
            tipoTarjetaId:    $comando->tipoTarjetaId,
            alias:            $comando->alias,
            numeroEncriptado: $comando->numeroEncriptado,
            ultimos4Digitos:  $comando->ultimos4Digitos,
            nombreTitular:    $comando->nombreTitular,
            fechaExpiracion:  $comando->fechaExpiracion,
            cvvEncriptado:    $comando->cvvEncriptado,
            bancoEmisor:      $comando->bancoEmisor,
            creadoEn:         $tarjetaExistente->obtenerCreadoEn(),
        );

        return $this->repositorioTarjeta->guardar($tarjetaActualizada);
    }
}
