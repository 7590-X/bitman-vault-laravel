{{--
    Componente para mostrar los metadatos de una Nota Segura Enviada seleccionada.
    REGLA 6: El contenido texto de la nota NO se expone ni al emisor.
--}}
<div x-data="EnviarNotasDetalleComponent" class="h-full max-w-xl flex flex-col min-h-0 overflow-hidden">

    {{-- Encabezado del Detalle --}}
    <x-share.panel-header>
        <x-slot:icono>
            <x-icon.send-note class="w-5 h-5 text-blue-400" />
        </x-slot:icono>
        <x-slot:titulo>
            <span x-text="notaSeleccionada.titulo"></span>
        </x-slot:titulo>
        <x-slot:subtitulo>
            <span class="font-mono text-slate-400" x-text="`Para: ${notaSeleccionada.correo_destino}`"></span>
        </x-slot:subtitulo>
    </x-share.panel-header>

    {{-- Cuerpo con los campos del detalle (scrolleable) --}}
    <div class="max-w-xl flex-1 overflow-y-auto min-h-0 px-8 py-6 space-y-4">

        {{-- Estado --}}
        <x-share.campo-detalle etiqueta="Estado del Envío">
            <div class="flex items-center">
                <x-share.badge
                    :indicador="false"
                    :estado="'notaSeleccionada.estado'"
                    :etiqueta="'notaSeleccionada.estado_etiqueta'" />
            </div>
        </x-share.campo-detalle>

        {{-- Correo Destino --}}
        <x-share.campo-detalle etiqueta="Correo Destino" :copiable="true">
            <span x-text="notaSeleccionada.correo_destino"></span>
        </x-share.campo-detalle>

        {{-- Código de Apertura --}}
        <x-share.campo-detalle etiqueta="Código de Apertura" :copiable="true">
            <span class="font-mono text-blue-400 font-bold tracking-widest" x-text="notaSeleccionada.codigo_apertura"></span>
        </x-share.campo-detalle>

        {{-- Enlace de Acceso --}}
        <x-share.campo-detalle etiqueta="Enlace Único de Acceso" :copiable="true">
            <span class="font-mono text-xs text-slate-300 break-all" x-text="notaSeleccionada.url_acceso"></span>
        </x-share.campo-detalle>

        {{-- Tiempo de Lectura --}}
        <x-share.campo-detalle etiqueta="Tiempo de Lectura Permitido">
            <span x-text="`${notaSeleccionada.duracion_visualizacion_segundos} segundos`"></span>
        </x-share.campo-detalle>

        {{-- Fecha Creación --}}
        <x-share.campo-detalle etiqueta="Fecha de Emisión">
            <span x-text="notaSeleccionada.creado_en || '—'"></span>
        </x-share.campo-detalle>

        {{-- Fecha Expiración --}}
        <x-share.campo-detalle etiqueta="Expira En">
            <span x-text="notaSeleccionada.expira_en || '—'"></span>
        </x-share.campo-detalle>

        {{-- Fecha Apertura (condicional) --}}
        <template x-if="notaSeleccionada.aperturado_en">
            <x-share.campo-detalle etiqueta="Fecha de Apertura">
                <span x-text="notaSeleccionada.aperturado_en"></span>
            </x-share.campo-detalle>
        </template>

        {{-- Banner de Privacidad CIA (Regla 6) --}}
        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 text-xs leading-relaxed space-y-2 mt-4">
            <div class="flex items-center gap-2 font-semibold text-slate-200">
                <x-icon.lock class="w-4 h-4 text-blue-400 shrink-0" />
                <span>Privacidad de un solo uso (Cero Registro de Texto)</span>
            </div>
            <p class="text-slate-400">
                Por políticas de privacidad de un solo uso, el contenido original de la nota no se muestra al emisor. Una vez que el destinatario ingrese el código de apertura, el texto se mostrará por <strong x-text="`${notaSeleccionada.duracion_visualizacion_segundos} segundos`"></strong> y el contenido será permanentemente autodestruido de la base de datos.
            </p>
        </div>

    </div>

    {{-- Pie fijo con acciones --}}
    <div class="shrink-0 py-4 px-8 border-t border-slate-700/80 bg-slate-950 flex items-center justify-end gap-3">
        <x-share.button
            variante="danger"
            tipo="button"
            cargando="eliminando"
            @click="confirmarEliminacion()">
            Revocar / Eliminar
        </x-share.button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('EnviarNotasDetalleComponent', () => ({
            eliminando: false,

            async confirmarEliminacion() {
                const result = await window.Dialog.confirm({
                    title: `¿Revocar nota para ${this.notaSeleccionada?.correo_destino}?`,
                    text: 'Esta acción eliminará el registro de la nota y revocará el enlace de acceso.',
                    confirmText: 'Sí, revocar',
                    type: 'danger'
                });

                if (result.isConfirmed) {
                    await this.eliminarNota();
                }
            },

            async eliminarNota() {
                this.eliminando = true;
                const token = localStorage.getItem('jwt_token');

                try {
                    const response = await fetch(`/api/enviar-notas/${this.notaSeleccionada.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        window.toastr.info('Nota revocada correctamente', 'Información');
                        this.$dispatch('nota-enviada-eliminada', this.notaSeleccionada.id);
                    } else {
                        window.toastr.error('No se pudo revocar la nota', 'Error');
                    }
                } catch (e) {
                    window.toastr.error(e.message || 'Error de conexión', 'Error');
                } finally {
                    this.eliminando = false;
                }
            }
        }))
    })
</script>
@endpush