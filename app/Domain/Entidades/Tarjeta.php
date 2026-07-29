<?php

/**
 * Entidad de dominio que representa una tarjeta de crédito o débito
 * almacenada de forma cifrada por un usuario en su bóveda.
 * Corresponde a la tabla secretos.tarjetas.
 */

namespace App\Domain\Entidades;

use DateTimeImmutable;

final class Tarjeta
{
    /**
     * Crea una nueva instancia de la entidad Tarjeta.
     *
     * @param int|null          $id                 Nulo al crear; asignado por la BD al persistir.
     * @param int               $usuarioId          FK hacia secretos.usuarios.
     * @param int               $tipoTarjetaId      FK hacia secretos.tipos_tarjeta.
     * @param string            $alias              Nombre descriptivo asignado por el usuario.
     * @param string            $numeroEncriptado   Número completo cifrado en capa de aplicación.
     * @param string            $ultimos4Digitos    Últimos 4 dígitos en claro (solo referencia visual).
     * @param string            $nombreTitular      Nombre del titular impreso en la tarjeta.
     * @param DateTimeImmutable $fechaExpiracion    Fecha de vencimiento.
     * @param string            $cvvEncriptado      CVV cifrado en capa de aplicación.
     * @param string|null       $bancoEmisor        Nombre del banco o entidad emisora (opcional).
     * @param DateTimeImmutable|null $creadoEn      Timestamp de creación (lo asigna la BD).
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $usuarioId,
        private readonly int                $tipoTarjetaId,
        private readonly string             $alias,
        private readonly string             $numeroEncriptado,
        private readonly string             $ultimos4Digitos,
        private readonly string             $nombreTitular,
        private readonly DateTimeImmutable  $fechaExpiracion,
        private readonly string             $cvvEncriptado,
        private readonly ?string            $bancoEmisor    = null,
        private readonly ?DateTimeImmutable $creadoEn       = null,
    ) {}

    /** Retorna el identificador de la tarjeta (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del usuario propietario. */
    public function obtenerUsuarioId(): int
    {
        return $this->usuarioId;
    }

    /** Retorna el ID del tipo de tarjeta. */
    public function obtenerTipoTarjetaId(): int
    {
        return $this->tipoTarjetaId;
    }

    /** Retorna el alias descriptivo de la tarjeta. */
    public function obtenerAlias(): string
    {
        return $this->alias;
    }

    /** Retorna el número de tarjeta cifrado. */
    public function obtenerNumeroEncriptado(): string
    {
        return $this->numeroEncriptado;
    }

    /** Retorna los últimos 4 dígitos en claro. */
    public function obtenerUltimos4Digitos(): string
    {
        return $this->ultimos4Digitos;
    }

    /** Retorna el nombre del titular de la tarjeta. */
    public function obtenerNombreTitular(): string
    {
        return $this->nombreTitular;
    }

    /** Retorna la fecha de expiración de la tarjeta. */
    public function obtenerFechaExpiracion(): DateTimeImmutable
    {
        return $this->fechaExpiracion;
    }

    /** Retorna el CVV cifrado. */
    public function obtenerCvvEncriptado(): string
    {
        return $this->cvvEncriptado;
    }

    /** Retorna el nombre del banco emisor (puede ser null). */
    public function obtenerBancoEmisor(): ?string
    {
        return $this->bancoEmisor;
    }

    /** Retorna el timestamp de creación (null antes de persistir). */
    public function obtenerCreadoEn(): ?DateTimeImmutable
    {
        return $this->creadoEn;
    }
}
