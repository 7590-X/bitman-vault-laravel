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

    {{-- Alpine.js para la interactividad del menú --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
    <aside class="w-64 bg-[#1e2329] text-[#e0e2e6] flex flex-col h-full shadow-lg z-10 shrink-0 border-r border-[#2d333b] overflow-y-auto overflow-x-hidden">
        @yield('sidebar')
    </aside>

    {{-- Área Principal (Derecha) --}}
    <div class="flex-1 flex flex-col h-full bg-[#f8fafc]">
        {{-- Topbar (Opcional, barra superior) --}}
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0">
            <h2 id="topbar-title" class="text-lg font-semibold text-slate-800">Cargando...</h2>
            
            {{-- Menú de usuario / Logout --}}
            <div class="flex items-center gap-4">
                <span id="user-email-display" class="text-sm font-medium text-slate-600">...</span>
                <button 
                    onclick="logout()" 
                    class="text-sm text-slate-500 hover:text-red-600 font-medium transition-colors"
                >
                    Cerrar sesión
                </button>
            </div>
        </header>

        {{-- Contenedor SPA Dinámico --}}
        <main id="app-content" class="flex-1 overflow-y-auto p-6 md:p-8">
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
                    headers: { 'Authorization': `Bearer ${token}` }
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

        async function logout() {
            const token = localStorage.getItem('jwt_token');
            if (token) {
                try {
                    await fetch('/api/auth/logout', {
                        method: 'POST',
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                } catch(e) {}
            }
            localStorage.removeItem('jwt_token');
            window.location.href = '/login';
        }

        document.addEventListener('DOMContentLoaded', loadUserInfo);
    </script>
</body>
</html>
