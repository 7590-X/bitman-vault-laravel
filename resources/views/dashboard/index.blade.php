@extends('layouts.dashboard')

@section('titulo', 'Bitman — Bóveda')

@section('sidebar')
{{-- Contenedor Alpine para el Sidebar, controlando el hash actual para highlighting --}}
<div x-data="{ currentRoute: window.location.hash || '#/logins' }" @hashchange.window="currentRoute = window.location.hash" class="py-4">
    
    {{-- GRUPO: Bóveda Principal --}}
    <x-sidebar.grupo titulo="All items" :abierto="true" icono="🗃️">
        <x-sidebar.item href="#/logins" titulo="Logins" icono="🌐" />
        <x-sidebar.item href="#/tarjetas-credito" titulo="Tarjetas de crédito" icono="💳" />
        <x-sidebar.item href="#/tarjetas-debito" titulo="Tarjetas de débito" icono="💳" />
        <x-sidebar.item href="#/notas-seguras" titulo="Notas seguras" icono="📝" />
        <x-sidebar.item href="#/ssh-keys" titulo="SSH keys" icono="🔑" />
        <x-sidebar.item href="#/mfa" titulo="Códigos MFA (2FA)" icono="🔐" />
    </x-sidebar.grupo>

    <div class="my-2 border-t border-[#2d333b]"></div>

    {{-- GRUPO: Archivos --}}
    <x-sidebar.grupo titulo="Files" :abierto="false" icono="📁">
        <x-sidebar.item href="#/archivos-encriptados" titulo="Archivos encriptados" icono="📄" />
    </x-sidebar.grupo>

    <div class="my-2 border-t border-[#2d333b]"></div>

    {{-- GRUPO: Compartir (Send) --}}
    <x-sidebar.grupo titulo="Share & Send" :abierto="false" icono="✉️">
        <x-sidebar.item href="#/enviar-notas" titulo="Enviar Notas Seguras" icono="📤" />
        <x-sidebar.item href="#/enviar-archivos" titulo="Enviar Archivos" icono="📦" />
        <x-sidebar.item href="#/control-compartidos" titulo="Control de Accesos" icono="⚙️" />
    </x-sidebar.grupo>

    <div class="my-2 border-t border-[#2d333b]"></div>

    {{-- GRUPO: Seguridad Avanzada --}}
    <x-sidebar.grupo titulo="Seguridad Avanzada" :abierto="false" icono="🛡️">
        <x-sidebar.item href="#/dead-man-switch" titulo="Interruptor de Emergencia" icono="☠️" />
    </x-sidebar.grupo>

</div>
@endsection

@section('contenido')
{{-- Este contenido será reemplazado dinámicamente por el JS en el futuro --}}
<div id="dynamic-view" class="h-full flex flex-col items-center justify-center text-center">
    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200 max-w-lg w-full">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-500 mb-4 text-2xl">
            🏗️
        </div>
        <h3 id="view-title" class="text-xl font-bold text-slate-800 mb-2">Bienvenido a Bitman Vault</h3>
        <p class="text-slate-500 mb-6">Selecciona una opción del menú lateral para comenzar a gestionar tus secretos.</p>
        <p class="text-xs text-slate-400 font-mono" id="view-path">Ruta actual: /</p>
    </div>
</div>

<script>
    // Enrutador SPA básico (Vanilla JS)
    document.addEventListener('DOMContentLoaded', () => {
        const topbarTitle = document.getElementById('topbar-title');
        const viewTitle = document.getElementById('view-title');
        const viewPath = document.getElementById('view-path');
        
        // Mapeo básico de rutas a títulos
        const routeTitles = {
            '#/logins': 'Logins (Credenciales)',
            '#/tarjetas-credito': 'Tarjetas de Crédito',
            '#/tarjetas-debito': 'Tarjetas de Débito',
            '#/notas-seguras': 'Notas Seguras',
            '#/ssh-keys': 'SSH Keys',
            '#/mfa': 'Códigos MFA (2FA)',
            '#/archivos-encriptados': 'Archivos Encriptados',
            '#/enviar-notas': 'Enviar Notas Seguras',
            '#/enviar-archivos': 'Enviar Archivos Sensibles',
            '#/control-compartidos': 'Control de Enlaces Compartidos',
            '#/dead-man-switch': 'Interruptor de Emergencia (Dead Man Switch)'
        };

        function handleRoute() {
            const hash = window.location.hash || '#/logins';
            const title = routeTitles[hash] || 'Panel de Control';
            
            // Actualizar topbar
            topbarTitle.textContent = title;
            
            // Actualizar vista temporal
            viewTitle.textContent = title;
            viewPath.textContent = `Ruta actual: ${hash}`;
            
            // Si estuviéramos conectando la API completa aquí haríamos:
            // fetch(`/api/${hash.replace('#/', '')}`) ... y reemplazar innerHTML
        }

        // Escuchar cambios en la URL (clics en el sidebar)
        window.addEventListener('hashchange', handleRoute);
        
        // Inicializar ruta actual
        handleRoute();
    });
</script>
@endsection
