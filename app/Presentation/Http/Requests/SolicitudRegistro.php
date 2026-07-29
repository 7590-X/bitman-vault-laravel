<?php

/**
 * Form Request para validar el formulario de registro de usuario.
 * Centraliza todas las reglas de validación y mensajes de error en español.
 */

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudRegistro extends FormRequest
{
    /**
     * Autoriza la ejecución de esta solicitud para todos los usuarios.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retorna las reglas de validación del formulario de registro.
     */
    public function rules(): array
    {
        return [
            'nombre_completo'         => ['required', 'string', 'max:150'],
            'correo_electronico'      => ['required', 'email:rfc', 'max:255'],
            'contrasena'              => [
                'required',
                'string',
                'min:12',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).+$/',
            ],
            'contrasena_confirmation' => ['required', 'string'],
        ];
    }

    /**
     * Retorna los mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'nombre_completo.required'         => 'El nombre completo es obligatorio.',
            'nombre_completo.max'              => 'El nombre completo no puede superar los 150 caracteres.',
            'correo_electronico.required'      => 'El correo electrónico es obligatorio.',
            'correo_electronico.email'         => 'El correo electrónico no tiene un formato válido.',
            'correo_electronico.max'           => 'El correo electrónico no puede superar los 255 caracteres.',
            'contrasena.required'              => 'La contraseña maestra es obligatoria.',
            'contrasena.min'                   => 'La contraseña debe tener al menos 12 caracteres.',
            'contrasena.confirmed'             => 'La confirmación de contraseña no coincide.',
            'contrasena.regex'                 => 'La contraseña debe contener al menos una mayúscula, un número y un símbolo.',
            'contrasena_confirmation.required' => 'Debe confirmar su contraseña maestra.',
        ];
    }

    /**
     * Retorna los nombres de atributos personalizados en español.
     */
    public function attributes(): array
    {
        return [
            'nombre_completo'         => 'nombre completo',
            'correo_electronico'      => 'correo electrónico',
            'contrasena'              => 'contraseña maestra',
            'contrasena_confirmation' => 'confirmación de contraseña',
        ];
    }
}
