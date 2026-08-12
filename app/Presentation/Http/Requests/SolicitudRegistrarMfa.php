<?php

/**
 * Form Request para validar la solicitud de inicio de registro MFA.
 */

namespace App\Presentation\Http\Requests;

use App\Domain\Enums\MetodoEnvioMfa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolicitudRegistrarMfa extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metodo_envio' => ['nullable', 'string', Rule::enum(MetodoEnvioMfa::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'metodo_envio.enum' => 'El método de envío seleccionado no es válido (opciones: correo, sms).',
        ];
    }
}
