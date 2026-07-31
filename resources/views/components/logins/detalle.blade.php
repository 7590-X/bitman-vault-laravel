{{--
    Componente independiente para mostrar el detalle de un Login seleccionado.
    Accede al estado del componente padre (loginSeleccionado) a través del scope de Alpine.js.
    Reutilizable en cualquier vista que tenga disponible la variable `loginSeleccionado` en su contexto Alpine.
--}}
<div x-data="LoginDetalleComponent" class="h-full max-w-xl flex flex-col min-h-0 overflow-hidden">

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

    {{-- ─── Pie fijo con acciones ──────────────────────────────────────────── --}}
    <div class="shrink-0 py-4 px-8 border-t border-slate-700/80 bg-slate-950 flex items-center justify-end gap-3">
        <x-share.button
            variante="danger"
            tipo="button"
            @click="confirmarEliminacion()">
            Eliminar
        </x-share.button>
        <x-share.button
            variante="primary"
            tipo="button"
            @click="iniciarEdicion()">
            Actualizar
        </x-share.button>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('LoginDetalleComponent', () => ({
            async confirmarEliminacion() {
                const result = await window.Dialog.confirm({
                    title: `¿Eliminar ${this.loginSeleccionado?.nombre_sitio}?`,
                    text: '¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer y los datos se perderán de forma permanente.',
                    confirmText: 'Sí, eliminar',
                    type: 'danger'
                });

                if (result.isConfirmed) {
                    this.eliminarLogin();
                }
            },
            async eliminarLogin() {
                const token = localStorage.getItem('jwt_token');
                try {
                    const response = await fetch(`/api/logins/${this.loginSeleccionado.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok || response.status === 200 || response.status === 204) {
                        window.toastr.info('Login eliminado correctamente', 'Eliminado');
                        this.$dispatch('login-eliminado', this.loginSeleccionado.id);
                        this.loginSeleccionado = null;
                    } else {
                        window.toastr.error('No se pudo eliminar el login', 'Error')
                    }
                } catch (e) {
                    window.toastr.error(e.message, 'Error')
                }
            }
        }))
    })
</script>
@endpush