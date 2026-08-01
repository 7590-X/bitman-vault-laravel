{{--
    Componente maestro para la gestión de Llaves SSH en el Dashboard.
--}}
<div x-data="indexSshKeysComponent"
    @llave-ssh-creada.window="
        await cargarLlaves();
        if($event.detail)
            llaveSeleccionada = $event.detail;
            creando = false;"

    @llave-ssh-actualizada.window="
        await cargarLlaves();
        if($event.detail)
            llaveSeleccionada = $event.detail;
            editando = false;"

    @llave-ssh-eliminada.window="
        const eliminadoId = $event.detail
        llaves = llaves.filter(l => l.id !== eliminadoId)"

    class="h-full w-full flex border border-slate-800 bg-slate-900 overflow-hidden shadow-xl">

    {{-- Panel Izquierdo: Lista de Llaves SSH (Componente Independiente) --}}
    <div class="w-80 md:w-96 shrink-0 h-full">
        <x-ssh-keys.lista />
    </div>

    {{-- Panel Derecho: Formulario de Creación O Detalle de la Llave Seleccionada --}}
    <div class="flex-1 h-full flex flex-col overflow-hidden p-6"
        :class="(creando || editando) ? 'bg-slate-950' : 'bg-slate-950/60'">

        {{-- Modo Creación: Despliega el formulario embebido en el panel derecho (Placeholder) --}}
        <div x-show="creando" class="h-full">
            <div class="h-full flex flex-col items-center justify-center text-center p-8 bg-slate-900 rounded-xl border border-slate-700/50 border-dashed">
                <div class="w-16 h-16 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-blue-400 mb-4 shadow-inner">
                    <x-icon.key class="w-8 h-8 opacity-80" />
                </div>
                <h3 class="text-lg font-medium text-slate-200">Nueva Llave SSH</h3>
                <p class="text-sm text-slate-400 mt-2">El formulario para agregar una nueva llave SSH está en construcción.</p>
                
                <button type="button" @click="cancelar()" class="mt-6 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-sm font-medium transition-colors border border-slate-700">
                    Cancelar
                </button>
            </div>
        </div>

        {{-- Modo Edición: Despliega el formulario de edición en el panel derecho (Placeholder) --}}
        <div x-show="editando" class="h-full">
            <template x-if="editando && llaveSeleccionada">
                <div class="h-full flex flex-col items-center justify-center text-center p-8 bg-slate-900 rounded-xl border border-slate-700/50 border-dashed">
                    <h3 class="text-lg font-medium text-slate-200">Editar Llave SSH</h3>
                    <p class="text-sm text-slate-400 mt-2">El formulario para editar la llave SSH está en construcción.</p>
                    <button type="button" @click="cancelar()" class="mt-6 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-sm font-medium transition-colors border border-slate-700">
                        Cancelar
                    </button>
                </div>
            </template>
        </div>

        {{-- Modo Detalle: Despliega los datos de la llave seleccionada (Placeholder) --}}
        <template x-if="!creando && !editando && llaveSeleccionada">
            <div class="h-full flex flex-col items-center justify-center text-center p-8 bg-slate-900 rounded-xl border border-slate-700/50 border-dashed">
                <h3 class="text-lg font-medium text-slate-200" x-text="'Detalles de: ' + llaveSeleccionada.nombre"></h3>
                <p class="text-sm text-slate-400 mt-2">La vista de detalle para esta llave SSH está en construcción.</p>
            </div>
        </template>

        {{-- Modo Vacío: Cuando no hay selección activa --}}
        <template x-if="!creando && !editando && !llaveSeleccionada && !cargando">
            <div class="h-full flex flex-col items-center justify-center text-center p-8">
                <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 mb-4 shadow-inner">
                    <x-icon.vault class="w-8 h-8 opacity-40" />
                </div>
                <h3 class="text-base font-medium text-slate-300">Selecciona un elemento</h3>
                <p class="text-xs text-slate-500 max-w-xs mt-1">Haz clic sobre una llave SSH de la lista lateral para ver sus detalles.</p>
            </div>
        </template>

    </div>

</div>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('indexSshKeysComponent', () => ({
            llaves: [],
            cargando: true,
            error: null,
            llaveSeleccionada: null,
            creando: false,
            editando: false,
            mensajeExito: null,

            async init() {
                await this.cargarLlaves();
            },

            iniciarCreacion() {
                this.creando = true;
                this.editando = false;
                this.llaveSeleccionada = null; // Opcional: deseleccionar al crear
            },

            cancelar() {
                this.creando = false;
                this.editando = false;
                // Si hay llaves, volvemos a seleccionar la primera si no había una seleccionada
                if (this.llaves.length > 0 && !this.llaveSeleccionada) {
                    this.llaveSeleccionada = this.llaves[0];
                }
            },

            iniciarEdicion() {
                this.editando = true;
                this.creando = false;
            },

            seleccionarLlave(llave) {
                if (this.creando || this.editando) {
                    return;
                }
                this.llaveSeleccionada = llave;
            },

            async cargarLlaves() {
                this.cargando = true;
                this.error = null;
                const token = localStorage.getItem('jwt_token');

                try {
                    const response = await fetch('/api/llaves-ssh', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const json = await response.json();
                        // El controlador asume que devuelve un array directamente de llaves, según su implementación
                        this.llaves = json || [];
                        if (this.llaves.length > 0 && !this.llaveSeleccionada) {
                            this.llaveSeleccionada = this.llaves[0];
                        }
                    } else {
                        this.llaves = [];
                    }
                } catch (e) {
                    this.error = 'Error de conexión';
                    this.llaves = [];
                } finally {
                    this.cargando = false;
                }
            }
        }))
    })
</script>
@endpush
