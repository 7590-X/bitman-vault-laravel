<?php

/**
 * Form Request para validar las credenciales del formulario de inicio de sesión.
 * Centraliza las reglas de validación y mensajes de error en español.
 */

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudIniciarSesion extends FormRequest
{
    /**
     * Autoriza la ejecución para todos los usuarios (validación en el manejador).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retorna las reglas de validación de las credenciales de acceso.
     */
    public function rules(): array
    {
        return [
            'correo_electronico' => ['required', 'email:rfc', 'max:255'],
            'contrasena'         => ['required', 'string', 'min:1'],
        ];
    }

    /**
     * Retorna los mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email'    => 'El correo electrónico no tiene un formato válido.',
            'correo_electronico.max'      => 'El correo electrónico no puede superar los 255 caracteres.',
            'contrasena.required'         => 'La contraseña es obligatoria.',
        ];
    }

    /**
     * Retorna los nombres de atributos personalizados en español.
     */
    public function attributes(): array
    {
        return [
            'correo_electronico' => 'correo electrónico',
            'contrasena'         => 'contraseña',
        ];
    }
}
