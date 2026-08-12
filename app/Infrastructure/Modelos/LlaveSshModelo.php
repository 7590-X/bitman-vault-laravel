<?php

namespace App\Infrastructure\Modelos;

use Illuminate\Database\Eloquent\Model;

class LlaveSshModelo extends Model
{
    protected $table = 'secretos.llaves_ssh';
    
    // Deshabilitar timestamps predeterminados porque la tabla solo usa 'creado_en'
    public $timestamps = false;
    
    // Configurar Laravel para que maneje la fecha de creación en 'creado_en'
    const CREATED_AT = 'creado_en';

    protected $fillable = [
        'usuario_id',
        'nombre',
        'llave_privada_encriptada',
        'llave_publica',
        'frase_paso_encriptada',
        'creado_en',
    ];

    /**
     * Al arrancar el modelo, asegurarse de que `creado_en` se asigne
     * si no viene por defecto (Laravel maneja esto si `timestamps = true`,
     * pero como lo apagamos para saltar `updated_at`, lo inyectamos manualmente).
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->creado_en)) {
                $model->creado_en = $model->freshTimestamp();
            }
        });
    }

    protected $casts = [
        'creado_en' => 'datetime',
    ];
}
