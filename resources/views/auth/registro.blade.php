{{--
    Vista del formulario de registro de usuario.
    Implementa HU-01: Registro de Cuenta de Usuario (Bóveda Maestra).
    Sistema de diseño: paleta Slate/Blue/Semántica, rounded-xl para card, rounded-md para inputs.
--}}
@extends('layouts.app')

@section('titulo', 'Crear cuenta')
@section('descripcion', 'Crea tu cuenta en Bitman y comienza a proteger tus secretos de forma segura.')

@section('contenido')
<main class="flex min-h-screen items-center justify-center px-4 py-12 bg-slate-900">

    <div class="w-full max-w-md">

        {{-- Cabecera / Marca --}}
        <div class="mb-8 flex flex-col items-center">
            <x-logo size="lg" justify="center" />
            <p class="mt-3 text-sm font-normal text-slate-400">Tu bóveda de secretos personal</p>
        </div>

        {{-- Tarjeta del formulario --}}
        <div class="rounded-xl bg-white px-8 py-8 shadow-md border border-slate-200">

            <h2 class="mb-1 text-xl font-medium text-slate-900">Crear cuenta</h2>
            <p class="mb-6 text-sm font-normal text-slate-500">
                Completa los campos para registrarte. Tu contraseña maestra protege toda tu bóveda.
            </p>

            {{-- Alerta de éxito (redirigida desde otra pantalla) --}}
            @if (session('exito'))
            <div class="mb-5">
                <x-share.alerta tipo="exito" :mensaje="session('exito')" />
            </div>
            @endif

            {{-- Alerta de error general --}}
            @if ($errors->any() && !$errors->has('correo_electronico') && !$errors->has('nombre_completo') && !$errors->has('contrasena'))
            <div class="mb-5">
                <x-share.alerta tipo="error" mensaje="Por favor, corrige los errores del formulario." />
            </div>
            @endif

            {{-- Formulario --}}
            <form
                id="formulario-registro"
                method="POST"
                action="{{ route('registro.guardar') }}"
                novalidate
                class="flex flex-col gap-5">
                @csrf

                {{-- Nombre completo --}}
                <x-auth.campo-formulario
                    nombre="nombre_completo"
                    etiqueta="Nombre completo"
                    tipo="text"
                    placeholder="Ej. María García López"
                    :requerido="true"
                    maxlength="150"
                    autocomplete="name" />

                {{-- Correo electrónico --}}
                <x-auth.campo-formulario
                    nombre="correo_electronico"
                    etiqueta="Correo electrónico"
                    tipo="email"
                    placeholder="tu@correo.com"
                    :requerido="true"
                    autocomplete="email" />

                {{-- Contraseña maestra --}}
                <div class="flex flex-col gap-1">
                    <x-auth.campo-formulario
                        nombre="contrasena"
                        etiqueta="Contraseña maestra"
                        tipo="password"
                        placeholder="Mín. 12 caracteres"
                        :requerido="true"
                        autocomplete="new-password" />
                    <x-auth.indicador-fortaleza campo-objetivo="contrasena" />
                    <p class="text-xs font-normal text-slate-500">
                        Debe tener al menos 12 caracteres, una mayúscula, un número y un símbolo.
                    </p>
                </div>

                {{-- Confirmar contraseña --}}
                <div class="flex flex-col gap-1">
                    <x-auth.campo-formulario
                        nombre="contrasena_confirmation"
                        etiqueta="Confirmar contraseña maestra"
                        tipo="password"
                        placeholder="Repite tu contraseña"
                        :requerido="true"
                        autocomplete="new-password" />
                    <p id="mensaje-coincidencia" class="text-xs font-medium hidden"></p>
                </div>

                {{-- Botón de envío --}}
                <button
                    id="boton-registro"
                    type="submit"
                    class="mt-1 w-full rounded-md bg-blue-500 px-4 py-2.5
                           text-sm font-medium text-white
                           hover:bg-blue-700
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                           disabled:opacity-60 disabled:cursor-not-allowed
                           transition-colors duration-150">
                    Crear mi cuenta
                </button>

            </form>
        </div>

        {{-- Enlace a login --}}
        <p class="mt-6 text-center text-sm font-normal text-slate-400">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}"
                class="font-medium text-blue-400 hover:text-blue-300 hover:underline underline-offset-4 transition-colors">
                Inicia sesión
            </a>
        </p>

    </div>
</main>

{{-- Validación en tiempo real: coincidencia de contraseñas --}}
<script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            const contrasena = document.getElementById('contrasena');
            const confirmacion = document.getElementById('contrasena_confirmation');
            const mensaje = document.getElementById('mensaje-coincidencia');
            const boton = document.getElementById('boton-registro');

            if (!contrasena || !confirmacion || !mensaje || !boton) return;

            function verificarCoincidencia() {
                if (confirmacion.value.length === 0) {
                    mensaje.classList.add('hidden');
                    return;
                }

                const coinciden = contrasena.value === confirmacion.value;

                mensaje.classList.remove('hidden', 'text-emerald-700', 'text-red-700');

                if (coinciden) {
                    mensaje.textContent = '✓ Las contraseñas coinciden.';
                    mensaje.classList.add('text-emerald-700');
                    confirmacion.classList.remove('border-red-500', 'bg-red-50');
                } else {
                    mensaje.textContent = '✕ Las contraseñas no coinciden.';
                    mensaje.classList.add('text-red-700');
                    confirmacion.classList.add('border-red-500', 'bg-red-50');
                }
            }

            contrasena.addEventListener('input', verificarCoincidencia);
            confirmacion.addEventListener('input', verificarCoincidencia);
        });
    }());
</script>
@endsection