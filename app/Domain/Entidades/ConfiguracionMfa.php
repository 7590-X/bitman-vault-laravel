<?php

/**
 * Entidad de dominio que representa la configuración de doble factor
 * de autenticación (MFA) de un usuario. Relación 1 a 1 con el usuario.
 * Corresponde a la tabla secretos.configuraciones_mfa.
 */

namespace App\Domain\Entidades;

use App\Domain\Enums\MetodoEnvioMfa;
use DateTimeImmutable;

final class ConfiguracionMfa
{
    /**
     * Crea una nueva instancia de la entidad ConfiguracionMfa.
     *
     * @param int|null          $id                 Nulo al crear; asignado por la BD al persistir.
     * @param int               $usuarioId          FK hacia secretos.usuarios (1 a 1).
     * @param string            $secretoEncriptado  Secreto para generar códigos, cifrado en capa de aplicación.
     * @param MetodoEnvioMfa    $metodoEnvio        Canal de entrega del código MFA.
     * @param bool              $esActivo           Indica si el MFA está habilitado actualmente.
     * @param DateTimeImmutable|null $activadoEn    Timestamp de activación (lo asigna la BD).
     */
    public function __construct(
        private readonly ?int               $id,
        private readonly int                $usuarioId,
        private readonly string             $secretoEncriptado,
        private readonly MetodoEnvioMfa     $metodoEnvio = MetodoEnvioMfa::Correo,
        private readonly bool               $esActivo    = true,
        private readonly ?DateTimeImmutable $activadoEn  = null,
    ) {}

    /** Retorna el identificador de la configuración MFA (null antes de persistir). */
    public function obtenerId(): ?int
    {
        return $this->id;
    }

    /** Retorna el ID del usuario propietario. */
    public function obtenerUsuarioId(): int
    {
        return $this->usuarioId;
    }

    /** Retorna el secreto cifrado para la generación de códigos. */
    public function obtenerSecretoEncriptado(): string
    {
        return $this->secretoEncriptado;
    }

    /** Retorna el método de envío del código MFA. */
    public function obtenerMetodoEnvio(): MetodoEnvioMfa
    {
        return $this->metodoEnvio;
    }

    /** Indica si el MFA está habilitado actualmente. */
    public function esActivo(): bool
    {
        return $this->esActivo;
    }

    /** Retorna el timestamp de activación del MFA (null antes de persistir). */
    public function obtenerActivadoEn(): ?DateTimeImmutable
    {
        return $this->activadoEn;
    }
}
