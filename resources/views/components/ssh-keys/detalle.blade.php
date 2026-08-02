{{--
    Componente independiente para mostrar el detalle de una Llave SSH seleccionada.
    Accede al estado del componente padre (llaveSeleccionada) a través del scope de Alpine.js.
--}}
<div x-data="SshKeysDetalleComponent" class="h-full max-w-xl flex flex-col min-h-0 overflow-hidden">

    {{-- ─── Encabezado del Detalle ──────────────────────────────────────── --}}
    <x-share.panel-header>
        <x-slot:icono>
            <x-icon.key class="w-5 h-5" />
        </x-slot:icono>
        <x-slot:titulo>
            <span x-text="llaveSeleccionada.nombre"></span>
        </x-slot:titulo>
        <x-slot:subtitulo>
            <span class="font-mono text-xs">Llave SSH</span>
        </x-slot:subtitulo>
    </x-share.panel-header>

    {{-- ─── Cuerpo con los campos del detalle (scrolleable) ────────────── --}}
    <div class="max-w-xl flex-1 overflow-y-auto min-h-0 px-8 py-6 space-y-3">

        {{-- Campo: Llave Pública --}}
        <x-share.campo-detalle
            etiqueta="Llave Pública"
            :copiable="true"
            accion-nombre="Descargar"
            accion-icono="download"
            accion-click="descargarLlave(llaveSeleccionada.llave_publica, (llaveSeleccionada.nombre || 'id_rsa') + '.pub')">
            <span x-text="llaveSeleccionada.llave_publica || '—'" class="break-all whitespace-pre-wrap font-mono text-xs text-slate-300"></span>
        </x-share.campo-detalle>

        {{-- Campo: Llave Privada --}}
        <x-share.campo-detalle
            etiqueta="Llave Privada"
            :oculto="true"
            :copiable="true"
            accion-nombre="Descargar"
            accion-icono="download"
            accion-click="descargarLlave(llaveSeleccionada.llave_privada_encriptada, llaveSeleccionada.nombre || 'id_rsa')">
            <span x-text="llaveSeleccionada.llave_privada_encriptada || '—'" class="break-all whitespace-pre-wrap font-mono text-xs"></span>
        </x-share.campo-detalle>

        {{-- Campo: Frase de Paso --}}
        <template x-if="llaveSeleccionada.frase_paso_encriptada">
            <x-share.campo-detalle etiqueta="Frase de Paso (Passphrase)" :oculto="true" :copiable="true">
                <span x-text="llaveSeleccionada.frase_paso_encriptada" class="break-all font-mono"></span>
            </x-share.campo-detalle>
        </template>
        <template x-if="!llaveSeleccionada.frase_paso_encriptada">
            <x-share.campo-detalle etiqueta="Frase de Paso (Passphrase)">
                <span class="text-slate-500 italic">No tiene frase de paso</span>
            </x-share.campo-detalle>
        </template>

        {{-- Campo: Fecha Registro --}}
        <template x-if="llaveSeleccionada.creado_en">
            <x-share.campo-detalle etiqueta="Fecha Registro">
                <span x-text="llaveSeleccionada.creado_en"></span>
            </x-share.campo-detalle>
        </template>

    </div>

    {{-- ─── Pie fijo con acciones ──────────────────────────────────────────── --}}
    <div class="shrink-0 py-4 px-8 border-t border-slate-700/80 bg-slate-950 flex items-center justify-end gap-3">
        <x-share.button
            variante="danger"
            tipo="button"
            @click="enConstruccion()">
            Eliminar
        </x-share.button>
        <x-share.button
            variante="primary"
            tipo="button"
            @click="enConstruccion()">
            Actualizar
        </x-share.button>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('SshKeysDetalleComponent', () => ({
            enConstruccion() {
                window.toastr.info('Esta funcionalidad estará disponible próximamente.', 'En Construcción');
            },
            descargarLlave(contenido, nombreArchivo) {
                if (!contenido || contenido === '—') {
                    window.toastr.warning('No hay contenido para descargar.', 'Advertencia');
                    return;
                }
                const blob = new Blob([contenido], {
                    type: 'text/plain;charset=utf-8'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = nombreArchivo.toLowerCase().replace(/\s+/g, '_') || 'id_rsa';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }
        }))
    })
</script>
@endpush