<?php

/**
 * Modelo Eloquent que representa la tabla `secretos.usuarios`.
 * Implementa las convenciones de timestamps y constantes del sistema.
 */

namespace App\Infrastructure\Modelos;

use App\Domain\Enums\EstadoUsuario;
use Illuminate\Database\Eloquent\Model;

class UsuarioModelo extends Model
{
    /** Tabla asociada al modelo en el esquema secretos. */
    protected $table = 'secretos.usuarios';

    /** Nombre del campo de creación personalizado. */
    public const CREATED_AT = 'creado_en';

    /** Nombre del campo de actualización personalizado. */
    public const UPDATED_AT = 'actualizado_en';

    /** Campos asignables en masa. */
    protected $fillable = [
        'nombre_completo',
        'correo_electronico',
        'hash_contrasena',
        'sal_contrasena',
        'estado',
    ];

    /** Campos ocultos en la serialización. */
    protected $hidden = [
        'hash_contrasena',
        'sal_contrasena',
    ];

    /**
     * Retorna los casts de atributos del modelo.
     */
    protected function casts(): array
    {
        return [
            'estado'      => EstadoUsuario::class,
            'creado_en'   => 'datetime',
            'actualizado_en' => 'datetime',
        ];
    }
}
