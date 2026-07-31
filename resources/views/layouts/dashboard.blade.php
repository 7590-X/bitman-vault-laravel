<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de control de Bitman — Tu bóveda de secretos segura y personal.">
    <title>@yield('titulo', 'Bitman Dashboard')</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif



    {{-- Protección de rutas en el cliente (JWT) --}}
    <script>
        (function() {
            const token = localStorage.getItem('jwt_token');
            // Si intenta acceder al dashboard sin token, redirige a login
            if (!token) {
                window.location.replace('/login');
            }
        })();
    </script>
</head>

<body class="bg-slate-50 text-slate-900 min-h-screen antialiased overflow-hidden flex h-screen w-screen">

    {{-- Área del Sidebar (Izquierda) --}}
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col h-full shadow-lg z-10 shrink-0 border-r border-slate-800 overflow-y-auto overflow-x-hidden">
        @yield('sidebar')
    </aside>

    {{-- Área Principal (Derecha) --}}
    <div class="flex-1 flex flex-col h-full bg-slate-50">
        {{-- Topbar (Barra superior) --}}
        <x-dashboard.topbar />

        {{-- Contenedor SPA Dinámico --}}
        <main id="app-content" class="flex-1 overflow-y-auto">
            @yield('contenido')
        </main>
    </div>

    {{-- Lógica base del layout (Logout y Obtener Usuario) --}}
    <script>
        async function loadUserInfo() {
            const token = localStorage.getItem('jwt_token');
            if (!token) return;

            try {
                const response = await fetch('/api/auth/me', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    document.getElementById('user-email-display').textContent = data.correo_electronico;
                } else {
                    // Si el token falló, forzamos logout
                    localStorage.removeItem('jwt_token');
                    window.location.replace('/login');
                }
            } catch (e) {
                console.error('Error al cargar datos del usuario', e);
            }
        }
        document.addEventListener('DOMContentLoaded', loadUserInfo);
    </script>

    @stack('scripts')
</body>

</html>