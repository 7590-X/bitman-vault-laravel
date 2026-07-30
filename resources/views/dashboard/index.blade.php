@extends('layouts.dashboard')

@section('titulo', 'BITMAN Vault')

@section('sidebar')
{{-- Contenedor Alpine para el Sidebar, controlando el hash actual para highlighting --}}
<div x-data="{ currentRoute: window.location.hash || '#/' }" @hashchange.window="currentRoute = window.location.hash" class="py-4">
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
<x-dashboard.content-logins />

<script>
    // Enrutador SPA para actualización de títulos en la barra superior
    document.addEventListener('DOMContentLoaded', () => {
        const topbarTitle = document.getElementById('topbar-title');

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
            const hash = window.location.hash || '#/';
            const title = routeTitles[hash] || 'Panel de Control';

            if (topbarTitle) {
                topbarTitle.textContent = title;
            }
        }

        window.addEventListener('hashchange', handleRoute);
        handleRoute();
    });
</script>
@endsection