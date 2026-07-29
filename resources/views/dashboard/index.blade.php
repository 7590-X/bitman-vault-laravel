@extends('layouts.dashboard')

@section('titulo', 'Bitman — Bóveda')

@section('sidebar')
{{-- Contenedor Alpine para el Sidebar, controlando el hash actual para highlighting --}}
<div x-data="{ currentRoute: window.location.hash || '#/logins' }" @hashchange.window="currentRoute = window.location.hash" class="py-4">
    <div class="px-5 mb-6 mt-2">
        <x-logo size="md" justify="start" />
    </div>
    {{-- GRUPO: Bóveda Principal --}}
    <x-sidebar.grupo titulo="All items" :abierto="true" icono="vault">
        <x-sidebar.item href="#/logins" titulo="Logins" icono="globe" />
        <x-sidebar.item href="#/tarjetas-credito" titulo="Tarjetas de crédito" icono="credit-card" />
        <x-sidebar.item href="#/tarjetas-debito" titulo="Tarjetas de débito" icono="card" />
        <x-sidebar.item href="#/notas-seguras" titulo="Notas seguras" icono="document" />
        <x-sidebar.item href="#/ssh-keys" titulo="SSH keys" icono="key" />
        <x-sidebar.item href="#/mfa" titulo="Códigos MFA (2FA)" icono="shield-check" />
    </x-sidebar.grupo>

    <div class="my-2 border-t border-slate-800"></div>

    {{-- GRUPO: Archivos --}}
    <x-sidebar.grupo titulo="Files" :abierto="false" icono="folder">
        <x-sidebar.item href="#/archivos-encriptados" titulo="Archivos encriptados" icono="file-lock" />
    </x-sidebar.grupo>

    <div class="my-2 border-t border-slate-800"></div>

    {{-- GRUPO: Compartir (Send) --}}
    <x-sidebar.grupo titulo="Share & Send" :abierto="false" icono="share">
        <x-sidebar.item href="#/enviar-notas" titulo="Enviar Notas Seguras" icono="send-note" />
        <x-sidebar.item href="#/enviar-archivos" titulo="Enviar Archivos" icono="package" />
        <x-sidebar.item href="#/control-compartidos" titulo="Control de Accesos" icono="sliders" />
    </x-sidebar.grupo>

    <div class="my-2 border-t border-slate-800"></div>

    {{-- GRUPO: Seguridad Avanzada --}}
    <x-sidebar.grupo titulo="Seguridad Avanzada" :abierto="false" icono="shield">
        <x-sidebar.item href="#/dead-man-switch" titulo="Interruptor de Emergencia" icono="power" />
    </x-sidebar.grupo>

</div>
@endsection

@section('contenido')
<div id="dynamic-view" class="h-full flex flex-col items-center justify-center text-center p-6">
    <div class="bg-white p-10 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200 max-w-lg w-full relative overflow-hidden group">
        {{-- Decoración superior premium --}}
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-blue-400 opacity-80 group-hover:opacity-100 transition-opacity"></div>

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-6 text-2xl shadow-inner border border-blue-100">
            🛡️
        </div>
        <h3 id="view-title" class="text-2xl font-semibold text-slate-900 mb-3 tracking-tight">Bienvenido a Bitman Vault</h3>
        <p class="text-slate-600 mb-8 leading-relaxed">Selecciona una opción del menú lateral para comenzar a gestionar tus secretos y credenciales de manera segura.</p>

        <div class="inline-flex items-center px-3 py-1.5 rounded-md bg-slate-50 border border-slate-200">
            <p class="text-xs text-slate-500 font-mono" id="view-path">Ruta actual: /</p>
        </div>
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