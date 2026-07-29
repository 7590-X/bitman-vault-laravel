<?php

/**
 * Modelo Eloquent que representa la tabla `secretos.usuarios`.
 * Extiende Authenticatable para integrarse con el guard JWT de Laravel.
 * Implementa JWTSubject para la emisión y verificación de tokens.
 */

namespace App\Infrastructure\Modelos;

use App\Domain\Enums\EstadoUsuario;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class UsuarioModelo extends Authenticatable implements JWTSubject
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

    /** Campos ocultos en la serialización JSON. */
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
            'estado'         => EstadoUsuario::class,
            'creado_en'      => 'datetime',
            'actualizado_en' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // JWTSubject — requerido por php-open-source-saver/jwt-auth
    // -------------------------------------------------------------------------

    /**
     * Retorna el identificador que se almacenará como "sub" en el payload JWT.
     * Usamos el ID primario (BIGINT) del usuario.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Retorna claims personalizados adicionales para el payload del token.
     * Incluimos el correo para que el cliente pueda leerlo sin llamar a /me.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'correo' => $this->correo_electronico,
        ];
    }
}

