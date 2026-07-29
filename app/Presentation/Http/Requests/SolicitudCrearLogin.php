<?php

/**
 * Form Request para validar los datos al registrar una nueva credencial (login).
 */

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudCrearLogin extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_sitio'          => ['required', 'string', 'max:150'],
            'url'                   => ['nullable', 'string', 'max:2048'],
            'usuario_login'         => ['nullable', 'string', 'max:150'],
            'contrasena_encriptada' => ['required', 'string'],
            'notas'                 => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_sitio.required'          => 'El nombre del sitio es obligatorio.',
            'nombre_sitio.max'               => 'El nombre del sitio no puede superar los 150 caracteres.',
            'url.max'                        => 'La URL no puede superar los 2048 caracteres.',
            'usuario_login.max'              => 'El usuario/correo del sitio no puede superar los 150 caracteres.',
            'contrasena_encriptada.required' => 'La contraseña encriptada es obligatoria.',
        ];
    }
}
