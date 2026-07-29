<?php

/**
 * Modelo Eloquent que representa la tabla `secretos.logins`.
 * Mapea la persistencia de datos en el esquema `secretos`.
 */

namespace App\Infrastructure\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginModelo extends Model
{
    /** Tabla asociada al modelo en el esquema secretos. */
    protected $table = 'secretos.logins';

    /** Nombre del campo de creación personalizado. */
    public const CREATED_AT = 'creado_en';

    /** Nombre del campo de actualización personalizado. */
    public const UPDATED_AT = 'actualizado_en';

    /** Campos asignables en masa. */
    protected $fillable = [
        'usuario_id',
        'nombre_sitio',
        'url',
        'usuario_login',
        'contrasena_encriptada',
        'notas',
    ];

    /**
     * Retorna los casts de atributos del modelo.
     */
    protected function casts(): array
    {
        return [
            'creado_en'      => 'datetime',
            'actualizado_en' => 'datetime',
        ];
    }

    /**
     * Relación con el usuario propietario del login.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UsuarioModelo::class, 'usuario_id');
    }
}
