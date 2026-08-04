<?php

/**
 * Form Request para validar la creación de un Envío de Nota Segura.
 */

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudCrearEnvioNotaSegura extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'                          => 'required|string|max:150',
            'correo_destino'                  => 'required|email|max:255',
            'texto_nota'                      => 'required|string',
            'codigo_apertura'                 => 'nullable|string|max:50',
            'minutos_expiracion'              => 'nullable|integer|min:1|max:1440',
            'duracion_visualizacion_segundos' => 'nullable|integer|min:5|max:300',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'         => 'El título de la nota es obligatorio.',
            'correo_destino.required' => 'El correo electrónico del destinatario es obligatorio.',
            'correo_destino.email'    => 'Ingresa un correo electrónico válido.',
            'texto_nota.required'     => 'El contenido de la nota es obligatorio.',
        ];
    }
}
