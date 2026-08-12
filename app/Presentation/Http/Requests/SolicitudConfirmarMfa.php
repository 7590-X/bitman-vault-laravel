<?php

/**
 * Form Request para validar la solicitud de confirmación del código MFA.
 */

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudConfirmarMfa extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código de verificación es obligatorio.',
            'codigo.size'     => 'El código debe tener exactamente 6 dígitos.',
            'codigo.regex'    => 'El código debe ser numérico de 6 dígitos.',
        ];
    }
}
