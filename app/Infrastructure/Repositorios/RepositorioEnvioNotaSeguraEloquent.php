<?php

/**
 * Adaptador Eloquent del repositorio para EnvioNotaSegura.
 */

namespace App\Infrastructure\Repositorios;

use App\Domain\Entidades\EnvioNotaSegura;
use App\Domain\Enums\EstadoEnvioNota;
use App\Domain\Puertos\RepositorioEnvioNotaSegura;
use App\Infrastructure\Modelos\EnvioNotaSeguraModelo;
use DateTimeImmutable;

final class RepositorioEnvioNotaSeguraEloquent implements RepositorioEnvioNotaSegura
{
    public function guardar(EnvioNotaSegura $nota): EnvioNotaSegura
    {
        $modelo = EnvioNotaSeguraModelo::create([
            'usuario_id'                      => $nota->obtenerUsuarioId(),
            'titulo'                          => $nota->obtenerTitulo(),
            'correo_destino'                  => $nota->obtenerCorreoDestino(),
            'contenido_encriptado'            => $nota->obtenerContenidoEncriptado(),
            'codigo_apertura'                 => $nota->obtenerCodigoApertura(),
            'token_acceso'                    => $nota->obtenerTokenAcceso(),
            'estado'                          => $nota->obtenerEstado()->value,
            'duracion_visualizacion_segundos' => $nota->obtenerDuracionVisualizacionSegundos(),
            'expira_en'                       => $nota->obtenerExpiraEn()->format('Y-m-d H:i:s'),
            'aperturado_en'                   => $nota->obtenerAperturadoEn()?->format('Y-m-d H:i:s'),
        ]);

        return $this->mapearADominio($modelo);
    }

    public function buscarPorUsuarioId(int $usuarioId): array
    {
        $modelos = EnvioNotaSeguraModelo::where('usuario_id', $usuarioId)
            ->orderBy('id', 'desc')
            ->get();

        return $modelos->map(fn(EnvioNotaSeguraModelo $m) => $this->mapearADominio($m))->toArray();
    }

    public function buscarPorIdYUsuarioId(int $id, int $usuarioId): ?EnvioNotaSegura
    {
        $modelo = EnvioNotaSeguraModelo::where('id', $id)
            ->where('usuario_id', $usuarioId)
            ->first();

        return $modelo ? $this->mapearADominio($modelo) : null;
    }

    public function buscarPorTokenAcceso(string $tokenAcceso): ?EnvioNotaSegura
    {
        $modelo = EnvioNotaSeguraModelo::where('token_acceso', $tokenAcceso)->first();

        return $modelo ? $this->mapearADominio($modelo) : null;
    }

    public function marcarComoAperturadaYLimpiarContenido(int $id): void
    {
        EnvioNotaSeguraModelo::where('id', $id)->update([
            'estado'               => EstadoEnvioNota::APERTURADA->value,
            'contenido_encriptado' => null, // Eliminación del contenido (Self-Destruct)
            'aperturado_en'        => now(),
        ]);
    }

    public function eliminar(int $id, int $usuarioId): bool
    {
        return EnvioNotaSeguraModelo::where('id', $id)
            ->where('usuario_id', $usuarioId)
            ->delete() > 0;
    }

    private function mapearADominio(EnvioNotaSeguraModelo $modelo): EnvioNotaSegura
    {
        $estado = EstadoEnvioNota::tryFrom($modelo->estado) ?? EstadoEnvioNota::NO_APERTURADA;

        $expiraEn = DateTimeImmutable::createFromInterface($modelo->expira_en);
        $aperturadoEn = $modelo->aperturado_en ? DateTimeImmutable::createFromInterface($modelo->aperturado_en) : null;
        $creadoEn = $modelo->creado_en ? DateTimeImmutable::createFromInterface($modelo->creado_en) : null;
        $actualizadoEn = $modelo->actualizado_en ? DateTimeImmutable::createFromInterface($modelo->actualizado_en) : null;

        // Evaluación dinámica si ya expiró
        if ($estado === EstadoEnvioNota::NO_APERTURADA && new DateTimeImmutable() > $expiraEn) {
            $estado = EstadoEnvioNota::EXPIRADA;
        }

        return new EnvioNotaSegura(
            id:                              $modelo->id,
            usuarioId:                       $modelo->usuario_id,
            titulo:                          $modelo->titulo,
            correoDestino:                   $modelo->correo_destino,
            contenidoEncriptado:             $modelo->contenido_encriptado,
            codigoApertura:                  $modelo->codigo_apertura,
            tokenAcceso:                     $modelo->token_acceso,
            estado:                          $estado,
            duracionVisualizacionSegundos:   $modelo->duracion_visualizacion_segundos,
            expiraEn:                        $expiraEn,
            aperturadoEn:                    $aperturadoEn,
            creadoEn:                        $creadoEn,
            actualizadoEn:                   $actualizadoEn,
        );
    }
}
