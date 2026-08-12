<?php

/**
 * Modelo Eloquent que representa la tabla `secretos.tipos_tarjeta`.
 * Mapea la persistencia de datos del catálogo de tipos de tarjeta.
 */

namespace App\Infrastructure\Modelos;

use Illuminate\Database\Eloquent\Model;

final class TipoTarjetaModelo extends Model
{
    /** Tabla asociada al modelo en el esquema secretos. */
    protected $table = 'secretos.tipos_tarjeta';

    /** Llave primaria de la tabla. */
    protected $id = 'id';

    /** Indica si el modelo debe mantener timestamps (created_at/updated_at). */
    public $timestamps = false;

    /** Campos asignables en masa. */
    protected $fillable = [
        'id',
        'nombre',
    ];
}
