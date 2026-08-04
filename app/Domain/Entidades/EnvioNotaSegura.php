<?php

/**
 * Entidad de Dominio que representa un Envío de Nota Segura.
 */

namespace App\Domain\Entidades;

use App\Domain\Enums\EstadoEnvioNota;
use DateTimeImmutable;

final class EnvioNotaSegura
{
    public function __construct(
        private readonly ?int $id,
        private readonly int $usuarioId,
        private readonly string $titulo,
        private readonly string $correoDestino,
        private readonly ?string $contenidoEncriptado,
        private readonly string $codigoApertura,
        private readonly string $tokenAcceso,
        private readonly EstadoEnvioNota $estado,
        private readonly int $duracionVisualizacionSegundos,
        private readonly DateTimeImmutable $expiraEn,
        private readonly ?DateTimeImmutable $aperturadoEn,
        private readonly ?DateTimeImmutable $creadoEn = null,
        private readonly ?DateTimeImmutable $actualizadoEn = null,
    ) {}

    public function obtenerId(): ?int
    {
        return $this->id;
    }

    public function obtenerUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function obtenerTitulo(): string
    {
        return $this->titulo;
    }

    public function obtenerCorreoDestino(): string
    {
        return $this->correoDestino;
    }

    public function obtenerContenidoEncriptado(): ?string
    {
        return $this->contenidoEncriptado;
    }

    public function obtenerCodigoApertura(): string
    {
        return $this->codigoApertura;
    }

    public function obtenerTokenAcceso(): string
    {
        return $this->tokenAcceso;
    }

    public function obtenerEstado(): EstadoEnvioNota
    {
        return $this->estado;
    }

    public function obtenerDuracionVisualizacionSegundos(): int
    {
        return $this->duracionVisualizacionSegundos;
    }

    public function obtenerExpiraEn(): DateTimeImmutable
    {
        return $this->expiraEn;
    }

    public function obtenerAperturadoEn(): ?DateTimeImmutable
    {
        return $this->aperturadoEn;
    }

    public function obtenerCreadoEn(): ?DateTimeImmutable
    {
        return $this->creadoEn;
    }

    public function obtenerActualizadoEn(): ?DateTimeImmutable
    {
        return $this->actualizadoEn;
    }

    public function estaExpirada(): bool
    {
        return new DateTimeImmutable() > $this->expiraEn;
    }

    public function estaAperturada(): bool
    {
        return $this->estado === EstadoEnvioNota::APERTURADA || $this->aperturadoEn !== null;
    }
}
