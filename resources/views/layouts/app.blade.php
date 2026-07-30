<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('descripcion', 'Bitman — Tu bóveda de secretos segura y personal.')">
    <title>@yield('titulo', 'BITMAN') Vault</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    {{-- Protección de rutas en el cliente (JWT) --}}
    <script>
        (function() {
            const rutasPublicas = ['/login', '/registro'];
            const rutaActual = window.location.pathname;
            const tieneToken = localStorage.getItem('jwt_token') !== null;

            // Si intenta acceder a ruta protegida sin token
            if (!rutasPublicas.includes(rutaActual) && !tieneToken) {
                window.location.replace('/login');
            }

            // Si intenta acceder a login/registro pero ya está autenticado
            if (rutasPublicas.includes(rutaActual) && tieneToken) {
                window.location.replace('/');
            }
        })();
    </script>
</head>

<body class="bg-slate-50 min-h-screen antialiased"
    data-session-success="{{ session('success') }}"
    data-session-error="{{ session('error') }}"
    data-session-info="{{ session('info') }}"
    data-session-warning="{{ session('warning') }}">

    @yield('contenido')

</body>

</html>