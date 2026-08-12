<?php

/**
 * Modelo Eloquent que representa la tabla particionada `secretos.codigos_mfa`.
 */

namespace App\Infrastructure\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodigoMfaModelo extends Model
{
    /** Tabla asociada al modelo en el esquema secretos. */
    protected $table = 'secretos.codigos_mfa';

    /** Desactivar timestamps estándar de Laravel ya que se usan generado_en y expira_en. */
    public $timestamps = false;

    /** Campos asignables en masa. */
    protected $fillable = [
        'configuracion_mfa_id',
        'codigo',
        'generado_en',
        'expira_en',
        'es_usado',
    ];

    /**
     * Retorna los casts de atributos del modelo.
     */
    protected function casts(): array
    {
        return [
            'generado_en' => 'datetime',
            'expira_en'   => 'datetime',
            'es_usado'    => 'boolean',
        ];
    }

    /**
     * Relación con la configuración MFA a la que pertenece el código.
     */
    public function configuracion(): BelongsTo
    {
        return $this->belongsTo(ConfiguracionMfaModelo::class, 'configuracion_mfa_id');
    }
}
