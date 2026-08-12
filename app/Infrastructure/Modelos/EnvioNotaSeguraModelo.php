<?php

/**
 * Modelo Eloquent para la tabla secretos.envio_notas_seguras.
 */

namespace App\Infrastructure\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnvioNotaSeguraModelo extends Model
{
    protected $table = 'secretos.envio_notas_seguras';

    public const CREATED_AT = 'creado_en';
    public const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'correo_destino',
        'contenido_encriptado',
        'codigo_apertura',
        'token_acceso',
        'estado',
        'duracion_visualizacion_segundos',
        'expira_en',
        'aperturado_en',
    ];

    protected function casts(): array
    {
        return [
            'expira_en'     => 'datetime',
            'aperturado_en' => 'datetime',
            'creado_en'     => 'datetime',
            'actualizado_en'=> 'datetime',
            'duracion_visualizacion_segundos' => 'integer',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UsuarioModelo::class, 'usuario_id');
    }
}
