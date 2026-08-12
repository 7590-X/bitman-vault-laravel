{{--
    Componente independiente para la barra superior (Topbar / Header) del Dashboard.
--}}
<header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0">
    <h2 id="topbar-title" class="text-lg font-semibold text-slate-800">Cargando...</h2>

    {{-- Menú de usuario / Logout --}}
    <div class="flex items-center gap-4">
        <span id="user-email-display" class="text-sm font-medium text-slate-600">...</span>
        <button
            onclick="logout()"
            class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-red-600 font-medium transition-colors duration-200">
            <x-icon.logout class="w-4 h-4" />
            <span>Cerrar Sesión</span>
        </button>
    </div>
</header>
<script>
    async function logout() {
        const token = localStorage.getItem('jwt_token');
        if (token) {
            try {
                await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
            } catch (e) {}
        }
        localStorage.removeItem('jwt_token');
        window.location.href = '/login';
    }
</script>