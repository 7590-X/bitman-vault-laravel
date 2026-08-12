<?php

/**
 * Modelo Eloquent que representa la tabla `secretos.configuraciones_mfa`.
 */

namespace App\Infrastructure\Modelos;

use App\Domain\Enums\MetodoEnvioMfa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConfiguracionMfaModelo extends Model
{
    /** Tabla asociada al modelo en el esquema secretos. */
    protected $table = 'secretos.configuraciones_mfa';

    /** Desactivar timestamps estándar de Laravel ya que la tabla solo usa activado_en. */
    public $timestamps = false;

    /** Campos asignables en masa. */
    protected $fillable = [
        'usuario_id',
        'secreto_encriptado',
        'metodo_envio',
        'es_activo',
        'activado_en',
    ];

    /**
     * Retorna los casts de atributos del modelo.
     */
    protected function casts(): array
    {
        return [
            'metodo_envio' => MetodoEnvioMfa::class,
            'es_activo'    => 'boolean',
            'activado_en'  => 'datetime',
        ];
    }

    /**
     * Relación con el usuario propietario.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UsuarioModelo::class, 'usuario_id');
    }

    /**
     * Relación con los códigos MFA generados.
     */
    public function codigos(): HasMany
    {
        return $this->hasMany(CodigoMfaModelo::class, 'configuracion_mfa_id');
    }
}
