<?php

/**
 * Implementación Eloquent del repositorio de tarjetas.
 * Convierte modelos de Eloquent hacia entidades de Dominio y viceversa.
 */

namespace App\Infrastructure\Repositorios;

use App\Domain\Entidades\Tarjeta;
use App\Domain\Puertos\RepositorioTarjeta;
use App\Infrastructure\Modelos\TarjetaModelo;
use DateTimeImmutable;

class RepositorioTarjetaEloquent implements RepositorioTarjeta
{
    /**
     * Convierte una entidad de dominio Tarjeta a un arreglo para Eloquent.
     */
    private function mapearADb(Tarjeta $tarjeta): array
    {
        return [
            'usuario_id'        => $tarjeta->obtenerUsuarioId(),
            'tipo_tarjeta_id'   => $tarjeta->obtenerTipoTarjetaId(),
            'alias'             => $tarjeta->obtenerAlias(),
            'numero_encriptado' => $tarjeta->obtenerNumeroEncriptado(),
            'ultimos_4_digitos' => $tarjeta->obtenerUltimos4Digitos(),
            'nombre_titular'    => $tarjeta->obtenerNombreTitular(),
            'fecha_expiracion'  => $tarjeta->obtenerFechaExpiracion()->format('Y-m-d H:i:s'),
            'cvv_encriptado'    => $tarjeta->obtenerCvvEncriptado(),
            'banco_emisor'      => $tarjeta->obtenerBancoEmisor(),
        ];
    }

    /**
     * Convierte un modelo Eloquent TarjetaModelo a una entidad de dominio Tarjeta.
     */
    private function mapearADominio(TarjetaModelo $modelo): Tarjeta
    {
        return new Tarjeta(
            id:                 $modelo->id,
            usuarioId:          $modelo->usuario_id,
            tipoTarjetaId:      $modelo->tipo_tarjeta_id,
            alias:              $modelo->alias,
            numeroEncriptado:   $modelo->numero_encriptado,
            ultimos4Digitos:    $modelo->ultimos_4_digitos,
            nombreTitular:      $modelo->nombre_titular,
            fechaExpiracion:    new DateTimeImmutable($modelo->fecha_expiracion->toDateTimeString()),
            cvvEncriptado:      $modelo->cvv_encriptado,
            bancoEmisor:        $modelo->banco_emisor,
            creadoEn:           $modelo->creado_en ? new DateTimeImmutable($modelo->creado_en->toDateTimeString()) : null,
        );
    }

    public function guardar(Tarjeta $tarjeta): Tarjeta
    {
        $datos = $this->mapearADb($tarjeta);
        
        $modelo = TarjetaModelo::updateOrCreate(
            ['id' => $tarjeta->obtenerId()],
            $datos
        );

        return $this->mapearADominio($modelo);
    }

    public function buscarPorId(int $id): ?Tarjeta
    {
        $modelo = TarjetaModelo::find($id);

        if (!$modelo) {
            return null;
        }

        return $this->mapearADominio($modelo);
    }

    public function obtenerTodasPorUsuario(int $usuarioId): array
    {
        $modelos = TarjetaModelo::where('usuario_id', $usuarioId)->get();
        return $modelos->map(fn (TarjetaModelo $modelo) => $this->mapearADominio($modelo))->toArray();
    }

    public function eliminar(int $id): bool
    {
        return TarjetaModelo::where('id', $id)->delete() > 0;
    }
}
