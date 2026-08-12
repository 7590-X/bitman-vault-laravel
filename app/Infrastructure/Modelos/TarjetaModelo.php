<?php

/**
 * Modelo Eloquent que representa la tabla `secretos.tarjetas`.
 * Mapea la persistencia de datos en el esquema `secretos`.
 */

namespace App\Infrastructure\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarjetaModelo extends Model
{
    /** Tabla asociada al modelo en el esquema secretos. */
    protected $table = 'secretos.tarjetas';

    /** Nombre del campo de creación personalizado. */
    public const CREATED_AT = 'creado_en';

    /** Nombre del campo de actualización personalizado. */
    public const UPDATED_AT = 'actualizado_en';

    /** Campos asignables en masa. */
    protected $fillable = [
        'usuario_id',
        'tipo_tarjeta_id',
        'alias',
        'numero_encriptado',
        'ultimos_4_digitos',
        'nombre_titular',
        'fecha_expiracion',
        'cvv_encriptado',
        'banco_emisor',
    ];

    /**
     * Retorna los casts de atributos del modelo.
     */
    protected function casts(): array
    {
        return [
            'fecha_expiracion' => 'datetime',
            'creado_en'        => 'datetime',
            'actualizado_en'   => 'datetime',
        ];
    }

    /**
     * Relación con el usuario propietario de la tarjeta.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UsuarioModelo::class, 'usuario_id');
    }
}
