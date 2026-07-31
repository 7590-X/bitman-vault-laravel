<?php

/**
 * Manejador del comando para crear una nueva tarjeta.
 */

namespace App\Application\Comandos;

use App\Domain\Entidades\Tarjeta;
use App\Domain\Puertos\RepositorioTarjeta;

final class ManejadorCrearTarjeta
{
    public function __construct(
        private readonly RepositorioTarjeta $repositorioTarjeta,
    ) {}

    /**
     * Ejecuta el caso de uso de creación de tarjeta.
     */
    public function manejar(ComandoCrearTarjeta $comando): Tarjeta
    {
        $tarjeta = new Tarjeta(
            id:               null,
            usuarioId:        $comando->usuarioId,
            tipoTarjetaId:    $comando->tipoTarjetaId,
            alias:            $comando->alias,
            numeroEncriptado: $comando->numeroEncriptado,
            ultimos4Digitos:  $comando->ultimos4Digitos,
            nombreTitular:    $comando->nombreTitular,
            fechaExpiracion:  $comando->fechaExpiracion,
            cvvEncriptado:    $comando->cvvEncriptado,
            bancoEmisor:      $comando->bancoEmisor,
        );

        return $this->repositorioTarjeta->guardar($tarjeta);
    }
}
