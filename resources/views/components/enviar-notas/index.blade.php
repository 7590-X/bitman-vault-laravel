{{--
    Componente maestro para la gestión de Enviar Notas Seguras en el Dashboard.
--}}
<div x-data="indexEnviarNotasComponent"
    @nota-enviada-creada.window="
        await cargarNotas();
        if($event.detail)
            notaSeleccionada = $event.detail;
            creando = false;"

    @nota-enviada-eliminada.window="
        const eliminadoId = $event.detail;
        notas = notas.filter(n => n.id !== eliminadoId);
        notaSeleccionada = notas.length > 0 ? notas[0] : null;"

    class="h-full w-full flex border border-slate-800 bg-slate-900 overflow-hidden shadow-xl">

    {{-- Panel Izquierdo: Lista de Notas Seguras Enviadas --}}
    <div class="w-80 md:w-96 shrink-0 h-full">
        <x-enviar-notas.lista />
    </div>

    {{-- Panel Derecho: Formulario de Creación O Detalle de la Nota Seleccionada --}}
    <div class="flex-1 h-full flex flex-col overflow-hidden p-6"
        :class="creando ? 'bg-slate-950' : 'bg-slate-950/60'">

        {{-- Modo Creación --}}
        <div x-show="creando" class="h-full">
            <x-enviar-notas.formulario-crear />
        </div>

        {{-- Modo Detalle --}}
        <template x-if="!creando && notaSeleccionada">
            <x-enviar-notas.detalle />
        </template>

        {{-- Modo Vacío --}}
        <template x-if="!creando && !notaSeleccionada && !cargando">
            <div class="h-full flex flex-col items-center justify-center text-center p-8">
                <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 mb-4 shadow-inner">
                    <x-icon.send-note class="w-8 h-8 opacity-40 text-blue-400" />
                </div>
                <h3 class="text-base font-medium text-slate-300">Selecciona una nota enviada</h3>
                <p class="text-xs text-slate-500 max-w-xs mt-1">Haz clic sobre una nota de la lista lateral para consultar su estado, enlace de acceso y código de apertura.</p>
            </div>
        </template>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('indexEnviarNotasComponent', () => ({
            notas: [],
            cargando: true,
            error: null,
            notaSeleccionada: null,
            creando: false,

            async init() {
                await this.cargarNotas();
            },

            iniciarCreacion() {
                this.creando = true;
            },

            cancelar() {
                this.creando = false;
                if (this.notas.length > 0 && !this.notaSeleccionada) {
                    this.notaSeleccionada = this.notas[0];
                }
            },

            seleccionarNota(nota) {
                if (this.creando) {
                    return;
                }
                this.notaSeleccionada = nota;
            },

            async cargarNotas() {
                this.cargando = true;
                this.error = null;
                const token = localStorage.getItem('jwt_token');

                try {
                    const response = await fetch('/api/enviar-notas', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const json = await response.json();
                        this.notas = json.datos || [];
                        if (this.notas.length > 0 && !this.notaSeleccionada) {
                            this.notaSeleccionada = this.notas[0];
                        }
                    } else {
                        this.notas = [];
                    }
                } catch (e) {
                    this.error = 'Error de conexión';
                    this.notas = [];
                } finally {
                    this.cargando = false;
                }
            }
        }))
    })
</script>
@endpush
