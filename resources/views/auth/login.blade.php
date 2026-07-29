{{--
    Vista del formulario de inicio de sesión.
    Se comunica con la API de autenticación JWT.
--}}
@extends('layouts.app')

@section('titulo', 'Iniciar sesión')
@section('descripcion', 'Accede a tu bóveda de secretos de Bitman de forma segura.')

@section('contenido')
<main class="flex min-h-screen items-center justify-center px-4 py-12 bg-slate-50">

    <div class="w-full max-w-md">

        {{-- Cabecera / Marca --}}
        <div class="mb-8 text-center">
            <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500 shadow-md">
                <span class="text-2xl" aria-hidden="true">🔐</span>
            </div>
            <h1 class="text-3xl font-bold text-slate-900">BITMAN</h1>
            <p class="mt-1 text-sm font-normal text-slate-500">Tu bóveda de secretos personal</p>
        </div>

        {{-- Tarjeta del formulario --}}
        <div class="rounded-xl bg-white px-8 py-8 shadow-md border border-slate-200">

            <h2 class="mb-1 text-xl font-medium text-slate-900">Iniciar sesión</h2>
            <p class="mb-6 text-sm font-normal text-slate-500">
                Ingresa tu correo y contraseña maestra para acceder.
            </p>

            {{-- Contenedor de alerta dinámica --}}
            <div id="alerta-contenedor" class="mb-5 hidden">
                <x-alerta tipo="error" mensaje="" />
            </div>

            {{-- Formulario manejado por JS --}}
            <form
                id="formulario-login"
                novalidate
                class="flex flex-col gap-5"
            >
                {{-- Correo electrónico --}}
                <x-campo-formulario
                    nombre="correo_electronico"
                    etiqueta="Correo electrónico"
                    tipo="email"
                    placeholder="tu@correo.com"
                    :requerido="true"
                    autocomplete="email"
                />

                {{-- Contraseña maestra --}}
                <div class="flex flex-col gap-1">
                    <x-campo-formulario
                        nombre="contrasena"
                        etiqueta="Contraseña maestra"
                        tipo="password"
                        placeholder="Tu contraseña"
                        :requerido="true"
                        autocomplete="current-password"
                    />
                </div>

                {{-- Botón de envío --}}
                <button
                    id="boton-login"
                    type="submit"
                    class="mt-1 w-full rounded-md bg-blue-500 px-4 py-2.5
                           text-sm font-medium text-white
                           hover:bg-blue-700
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                           disabled:opacity-60 disabled:cursor-not-allowed
                           transition-colors duration-150"
                >
                    Entrar a mi bóveda
                </button>

            </form>
        </div>

        {{-- Enlace a registro --}}
        <p class="mt-6 text-center text-sm font-normal text-slate-500">
            ¿No tienes cuenta?
            <a href="{{ route('registro') }}"
               class="font-medium text-blue-700 hover:underline underline-offset-4">
                Crea una aquí
            </a>
        </p>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formulario-login');
    const boton = document.getElementById('boton-login');
    const alertaContenedor = document.getElementById('alerta-contenedor');
    const alertaTexto = alertaContenedor.querySelector('p'); // El <p> dentro del componente alerta

    if (!form || !boton || !alertaContenedor || !alertaTexto) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const correo = document.getElementById('correo_electronico').value;
        const contrasena = document.getElementById('contrasena').value;
        
        if (!correo || !contrasena) {
            mostrarError('Por favor, completa ambos campos.');
            return;
        }

        // Estado de carga
        boton.disabled = true;
        boton.textContent = 'Verificando...';
        alertaContenedor.classList.add('hidden');

        try {
            const respuesta = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    correo_electronico: correo,
                    contrasena: contrasena
                })
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                let mensaje = datos.mensaje || 'Credenciales inválidas.';
                if (datos.errors) {
                    const primerError = Object.values(datos.errors)[0][0];
                    mensaje = primerError;
                }
                mostrarError(mensaje);
                boton.disabled = false;
                boton.textContent = 'Entrar a mi bóveda';
                return;
            }

            // Éxito: guardar JWT y redirigir a inicio protegido
            localStorage.setItem('jwt_token', datos.access_token);
            window.location.href = '/';

        } catch (error) {
            mostrarError('Error de conexión. Revisa tu internet e intenta de nuevo.');
            boton.disabled = false;
            boton.textContent = 'Entrar a mi bóveda';
        }
    });

    function mostrarError(mensaje) {
        alertaTexto.textContent = mensaje;
        alertaContenedor.classList.remove('hidden');
    }
});
</script>
@endsection
