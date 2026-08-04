<?php

/**
 * Puerto (interfaz) para la gestión del estado y persistencia de MFA.
 * Define el contrato que debe cumplir la capa de infraestructura.
 */

namespace App\Domain\Puertos;

use App\Domain\Entidades\CodigoMfa;
use App\Domain\Entidades\ConfiguracionMfa;

interface RepositorioMfa
{
    /**
     * Busca la configuración MFA asociada a un usuario por su ID.
     */
    public function buscarConfiguracionPorUsuarioId(int $usuarioId): ?ConfiguracionMfa;

    /**
     * Guarda una nueva configuración MFA para un usuario.
     */
    public function guardarConfiguracion(ConfiguracionMfa $configuracion): ConfiguracionMfa;

    /**
     * Actualiza el estado o canal de una configuración MFA existente.
     */
    public function actualizarConfiguracion(ConfiguracionMfa $configuracion): void;

    /**
     * Desactiva la configuración MFA de un usuario.
     */
    public function desactivarConfiguracion(int $usuarioId): void;

    /**
     * Guarda un nuevo código MFA generado.
     */
    public function guardarCodigo(CodigoMfa $codigo): CodigoMfa;

    /**
     * Obtiene el último código MFA generado para una configuración que aún no haya expirado ni sido consumido.
     */
    public function obtenerUltimoCodigoValido(int $configuracionMfaId): ?CodigoMfa;

    /**
     * Marca un código MFA como consumido/usado.
     */
    public function marcarCodigoComoUsado(int $codigoId): void;
}
