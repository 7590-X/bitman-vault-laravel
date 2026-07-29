<?php

/**
 * Controlador HTTP para el proceso de registro de nuevos usuarios.
 * Actúa como punto de entrada entre la capa HTTP y la capa de aplicación.
 */

namespace App\Presentation\Http\Controllers;

use App\Application\Comandos\ComandoRegistrarUsuario;
use App\Application\Comandos\ManejadorRegistrarUsuario;
use App\Presentation\Http\Requests\SolicitudRegistro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use RuntimeException;

class ControladorRegistro extends Controller
{
    /**
     * Inyecta el manejador del caso de uso de registro.
     */
    public function __construct(
        private readonly ManejadorRegistrarUsuario $manejador,
    ) {}

    /**
     * Muestra el formulario de registro de usuario.
     */
    public function mostrar(): View
    {
        return view('auth.registro');
    }

    /**
     * Procesa el formulario de registro y crea la cuenta del usuario.
     */
    public function registrar(SolicitudRegistro $solicitud): RedirectResponse
    {
        $comando = new ComandoRegistrarUsuario(
            nombreCompleto: $solicitud->validated('nombre_completo'),
            correoElectronico: $solicitud->validated('correo_electronico'),
            contrasena: $solicitud->validated('contrasena'),
        );

        try {
            $this->manejador->manejar($comando);
        } catch (RuntimeException $excepcion) {
            return redirect()
                ->route('registro')
                ->withInput($solicitud->except(['contrasena', 'contrasena_confirmation']))
                ->withErrors(['correo_electronico' => $excepcion->getMessage()]);
        }

        return redirect()
            ->route('login')
            ->with('exito', 'Cuenta creada exitosamente. Ahora puedes iniciar sesión.');
    }
}
