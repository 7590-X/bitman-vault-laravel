{{--
    Componente independiente para mostrar el detalle de un Login seleccionado.
    Accede al estado del componente padre (loginSeleccionado) a través del scope de Alpine.js.
    Reutilizable en cualquier vista que tenga disponible la variable `loginSeleccionado` en su contexto Alpine.
--}}
<div class="h-full flex flex-col min-h-0 overflow-hidden">

    {{-- ─── Encabezado del Detalle ──────────────────────────────────────── --}}
    <x-share.panel-header>
        <x-slot:icono>
            <x-icon.globe class="w-5 h-5" />
        </x-slot:icono>
        <x-slot:titulo>
            <span x-text="loginSeleccionado.nombre_sitio"></span>
        </x-slot:titulo>
        <x-slot:subtitulo>
            <span class="font-mono" x-text="loginSeleccionado.url || 'Sin URL registrada'"></span>
        </x-slot:subtitulo>
    </x-share.panel-header>

    {{-- ─── Cuerpo con los campos del detalle (scrolleable) ────────────── --}}
    <div class="max-w-xl flex-1 overflow-y-auto min-h-0 px-8 py-6 space-y-3">

        {{-- Campo: Usuario / Correo --}}
        <x-share.campo-detalle etiqueta="Usuario / Correo" :copiable="true">
            <span x-text="loginSeleccionado.usuario_login || '—'"></span>
        </x-share.campo-detalle>

        {{-- Campo: Contraseña Encriptada --}}
        <x-share.campo-detalle etiqueta="Contraseña" :oculto="true" :copiable="true">
            <span x-text="loginSeleccionado.contrasena_encriptada || ''"></span>
        </x-share.campo-detalle>

        {{-- Campo: Notas adicionales (condicional) --}}
        <template x-if="loginSeleccionado.notas">
            <x-share.campo-detalle etiqueta="Notas">
                <span x-text="loginSeleccionado.notas"></span>
            </x-share.campo-detalle>
        </template>

        {{-- Campo: Fecha Creación --}}
        <template x-if="loginSeleccionado.creado_en">
            <x-share.campo-detalle etiqueta="Fecha Registro">
                <span x-text="loginSeleccionado.creado_en"></span>
            </x-share.campo-detalle>
        </template>

        {{-- Campo: Fecha Modificación --}}
        <template x-if="loginSeleccionado.actualizado_en">
            <x-share.campo-detalle etiqueta="Fecha Registro">
                <span x-text="loginSeleccionado.actualizado_en"></span>
            </x-share.campo-detalle>
        </template>

    </div>
</div>