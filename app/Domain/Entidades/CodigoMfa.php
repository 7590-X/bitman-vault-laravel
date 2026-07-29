<?php

/**
 * Entidad de dominio que representa un código MFA generado y su estado.
 * Tabla particionada bimestralmente por generado_en.
 * Corresponde a la tabla secretos.codigos_mfa.
 */

namespace App\Domain\Entidades;

use DateTimeImmutable;

final class CodigoMfa
{
    /**
     * Crea una nueva instancia de la entidad CodigoMfa.
     *
     * @param int|null          $id                   Nulo al crear; asignado por la BD al persistir.
     * @param int               $configuracionMfaId   FK hacia secretos.configuraciones_mfa.
     * @param string            $codigo               Código numérico de exactamente 6 dígitos.
     * @param DateTimeImmutable $expiraEn             Fecha y hora en que el código deja de ser válido.
     * @param bool              $esUsado              Indica si el código ya fue consumido.
     * @param DateTimeImmutable|null $generadoEn      Timestamp de generación y clave de partición.
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $configuracionMfaId,
        private readonly string             $codigo,
        private readonly DateTimeImmutable  $expiraEn,
        private readonly bool               $esUsado    = false,
        private readonly ?DateTimeImmutable $generadoEn = null,
    ) {}

    /** Retorna el identificador del código MFA (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID de la configuración MFA que generó el código. */
    public function obtenerConfiguracionMfaId(): int
    {
        return $this->configuracionMfaId;
    }

    /** Retorna el código numérico de 6 dígitos. */
    public function obtenerCodigo(): string
    {
        return $this->codigo;
    }

    /** Retorna el timestamp de expiración del código. */
    public function obtenerExpiraEn(): DateTimeImmutable
    {
        return $this->expiraEn;
    }

    /** Indica si el código ya fue utilizado. */
    public function esUsado(): bool
    {
        return $this->esUsado;
    }

    /** Retorna el timestamp de generación y clave de partición (null antes de persistir). */
    public function obtenerGeneradoEn(): ?DateTimeImmutable
    {
        return $this->generadoEn;
    }

    /** Indica si el código sigue siendo válido (no usado y no expirado). */
    public function esValido(): bool
    {
        return !$this->esUsado && new DateTimeImmutable() <= $this->expiraEn;
    }
}
