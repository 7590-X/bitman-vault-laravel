<?php

/**
 * Implementación Eloquent del repositorio MFA.
 * Adaptador de infraestructura que satisface el puerto RepositorioMfa.
 */

namespace App\Infrastructure\Repositorios;

use App\Domain\Entidades\CodigoMfa;
use App\Domain\Entidades\ConfiguracionMfa;
use App\Domain\Enums\MetodoEnvioMfa;
use App\Domain\Puertos\RepositorioMfa;
use App\Infrastructure\Modelos\CodigoMfaModelo;
use App\Infrastructure\Modelos\ConfiguracionMfaModelo;
use DateTimeImmutable;

final class RepositorioMfaEloquent implements RepositorioMfa
{
    /**
     * Busca la configuración MFA asociada a un usuario.
     */
    public function buscarConfiguracionPorUsuarioId(int $usuarioId): ?ConfiguracionMfa
    {
        /** @var ConfiguracionMfaModelo|null $modelo */
        $modelo = ConfiguracionMfaModelo::where('usuario_id', $usuarioId)->first();

        if ($modelo === null) {
            return null;
        }

        return $this->mapearConfiguracionADominio($modelo);
    }

    /**
     * Guarda una nueva configuración MFA.
     */
    public function guardarConfiguracion(ConfiguracionMfa $configuracion): ConfiguracionMfa
    {
        $modelo = ConfiguracionMfaModelo::create([
            'usuario_id'         => $configuracion->obtenerUsuarioId(),
            'secreto_encriptado' => $configuracion->obtenerSecretoEncriptado(),
            'metodo_envio'       => $configuracion->obtenerMetodoEnvio()->value,
            'es_activo'          => $configuracion->esActivo(),
            'activado_en'        => now(),
        ]);

        return $this->mapearConfiguracionADominio($modelo);
    }

    /**
     * Actualiza el estado o canal de una configuración MFA existente.
     */
    public function actualizarConfiguracion(ConfiguracionMfa $configuracion): void
    {
        ConfiguracionMfaModelo::where('usuario_id', $configuracion->obtenerUsuarioId())
            ->update([
                'secreto_encriptado' => $configuracion->obtenerSecretoEncriptado(),
                'metodo_envio'       => $configuracion->obtenerMetodoEnvio()->value,
                'es_activo'          => $configuracion->esActivo(),
            ]);
    }

    /**
     * Desactiva el MFA para un usuario específico.
     */
    public function desactivarConfiguracion(int $usuarioId): void
    {
        ConfiguracionMfaModelo::where('usuario_id', $usuarioId)
            ->update([
                'es_activo' => false,
            ]);
    }

    /**
     * Guarda un nuevo código MFA generado.
     */
    public function guardarCodigo(CodigoMfa $codigo): CodigoMfa
    {
        $modelo = CodigoMfaModelo::create([
            'configuracion_mfa_id' => $codigo->obtenerConfiguracionMfaId(),
            'codigo'               => $codigo->obtenerCodigo(),
            'generado_en'          => now(),
            'expira_en'            => $codigo->obtenerExpiraEn()->format('Y-m-d H:i:s'),
            'es_usado'             => $codigo->esUsado(),
        ]);

        return $this->mapearCodigoADominio($modelo);
    }

    /**
     * Obtiene el último código MFA no usado y vigente para la configuración.
     */
    public function obtenerUltimoCodigoValido(int $configuracionMfaId): ?CodigoMfa
    {
        /** @var CodigoMfaModelo|null $modelo */
        $modelo = CodigoMfaModelo::where('configuracion_mfa_id', $configuracionMfaId)
            ->where('es_usado', false)
            ->where('expira_en', '>=', now())
            ->orderBy('id', 'desc')
            ->first();

        if ($modelo === null) {
            return null;
        }

        return $this->mapearCodigoADominio($modelo);
    }

    /**
     * Marca un código MFA como consumido/usado.
     */
    public function marcarCodigoComoUsado(int $codigoId): void
    {
        CodigoMfaModelo::where('id', $codigoId)->update(['es_usado' => true]);
    }

    /**
     * Convierte un modelo Eloquent ConfiguracionMfaModelo a Entidad de Dominio.
     */
    private function mapearConfiguracionADominio(ConfiguracionMfaModelo $modelo): ConfiguracionMfa
    {
        $metodoEnvio = $modelo->metodo_envio instanceof MetodoEnvioMfa
            ? $modelo->metodo_envio
            : MetodoEnvioMfa::from($modelo->metodo_envio);

        $activadoEn = $modelo->activado_en
            ? DateTimeImmutable::createFromInterface($modelo->activado_en)
            : null;

        return new ConfiguracionMfa(
            id:                $modelo->id,
            usuarioId:         $modelo->usuario_id,
            secretoEncriptado: $modelo->secreto_encriptado,
            metodoEnvio:       $metodoEnvio,
            esActivo:          (bool) $modelo->es_activo,
            activadoEn:        $activadoEn,
        );
    }

    /**
     * Convierte un modelo Eloquent CodigoMfaModelo a Entidad de Dominio.
     */
    private function mapearCodigoADominio(CodigoMfaModelo $modelo): CodigoMfa
    {
        $generadoEn = $modelo->generado_en
            ? DateTimeImmutable::createFromInterface($modelo->generado_en)
            : null;

        $expiraEn = DateTimeImmutable::createFromInterface($modelo->expira_en);

        return new CodigoMfa(
            id:                  $modelo->id,
            configuracionMfaId:  $modelo->configuracion_mfa_id,
            codigo:              $modelo->codigo,
            expiraEn:            $expiraEn,
            esUsado:             (bool) $modelo->es_usado,
            generadoEn:          $generadoEn,
        );
    }
}
