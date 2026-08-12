<?php

/**
 * Form Request para validar los datos al actualizar una tarjeta existente.
 */

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudActualizarTarjeta extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_tarjeta_id'   => ['required', 'integer'],
            'alias'             => ['required', 'string', 'max:100'],
            'numero_encriptado' => ['required', 'string'],
            'ultimos_4_digitos' => ['required', 'string', 'size:4'],
            'nombre_titular'    => ['required', 'string', 'max:150'],
            'fecha_expiracion'  => ['required', 'date'],
            'cvv_encriptado'    => ['required', 'string'],
            'banco_emisor'      => ['nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_tarjeta_id.required'   => 'El tipo de tarjeta es obligatorio.',
            'alias.required'             => 'El alias es obligatorio.',
            'numero_encriptado.required' => 'El número encriptado de la tarjeta es obligatorio.',
            'ultimos_4_digitos.required' => 'Los últimos 4 dígitos son obligatorios.',
            'ultimos_4_digitos.size'     => 'Los últimos 4 dígitos deben ser exactamente 4 caracteres.',
            'nombre_titular.required'    => 'El nombre del titular es obligatorio.',
            'fecha_expiracion.required'  => 'La fecha de expiración es obligatoria.',
            'fecha_expiracion.date'      => 'La fecha de expiración debe ser una fecha válida.',
            'cvv_encriptado.required'    => 'El CVV encriptado es obligatorio.',
        ];
    }
}
