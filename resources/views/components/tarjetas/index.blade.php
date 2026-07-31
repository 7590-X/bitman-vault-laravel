<div x-data="indexTarjetaComponent"
    @tarjeta-creada.window="creando = false; cargarTarjetas()"
    @cancelar-creacion.window="creando = false"
    class="h-full w-full bg-slate-900 overflow-y-auto p-6 lg:p-10">

    <div class="max-w-7xl mx-auto">
        {{-- Encabezado --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight flex items-center gap-3">
                    <x-icon.credit-card class="w-8 h-8 text-blue-500" />
                    Tarjetas de Crédito y Débito
                </h2>
                <p class="text-slate-400 mt-1 text-sm">Gestiona tus tarjetas de forma segura en tu bóveda personal.</p>
            </div>
            <x-share.button @click="iniciarCreacion()" variante="primary">
                <x-icon.plus class="w-5 h-5" />
                <span>Nueva Tarjeta</span>
            </x-share.button>
        </div>

        {{-- Contenedor de Vistas (Grid vs Split) --}}
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            {{-- Panel Izquierdo: Formulario de Creación (Visible solo al crear) --}}
            <template x-if="creando">
                <div class="w-full lg:w-[45%] shrink-0 transition-all duration-300">
                    <x-tarjetas.formulario-crear />
                </div>
            </template>

            {{-- Panel Derecho / Completo: Tarjetas --}}
            <div class="w-full transition-all duration-300" :class="creando ? 'lg:w-[55%]' : 'lg:w-full'">

                {{-- Estado de Carga --}}
                <template x-if="cargando">
                    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-slate-700 border-t-blue-500 mb-4"></div>
                        <p class="font-medium text-sm">Cargando tarjetas seguras...</p>
                    </div>
                </template>

                {{-- Estado Vacío --}}
                <template x-if="!cargando && tarjetas.length === 0">
                    <div class="flex flex-col items-center justify-center py-20 text-center bg-slate-800/30 rounded-3xl border border-slate-700/50 border-dashed">
                        <div class="w-20 h-20 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 mb-6 shadow-inner">
                            <x-icon.credit-card class="w-10 h-10 opacity-60" />
                        </div>
                        <h3 class="text-xl font-semibold text-slate-200">No hay tarjetas registradas</h3>
                        <p class="text-slate-400 mt-2 max-w-md">Guarda tu primera tarjeta para mantenerla segura y tenerla a la mano cuando la necesites.</p>
                        <button @click="iniciarCreacion()" class="mt-6 text-blue-400 hover:text-blue-300 font-medium text-sm underline underline-offset-4 transition-colors">
                            Añadir una tarjeta ahora
                        </button>
                    </div>
                </template>

                {{-- Grid de Tarjetas --}}
                <template x-if="!cargando && tarjetas.length > 0">
                    <div class="grid gap-6 transition-all duration-300" :class="creando ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-1' : 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3'">
                        <template x-for="tarjeta in tarjetas" :key="tarjeta.id">
                            <x-tarjetas.item alpineObject="tarjeta" @click="seleccionarTarjeta(tarjeta)" />
                        </template>
                    </div>
                </template>

            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('indexTarjetaComponent', () => ({
            tarjetas: [],
            cargando: true,
            error: null,
            tarjetaSeleccionada: null,
            creando: false,
            editando: false,

            async init() {
                await this.cargarTarjetas();
            },

            iniciarCreacion() {
                this.creando = true;
            },

            seleccionarTarjeta(tarjeta) {
                this.tarjetaSeleccionada = tarjeta;
                this.editando = true;
                // Lógica futura para abrir modal/panel de detalle/edición
            },

            formatearFecha(fechaStr) {
                if (!fechaStr) return '**/**';
                const fecha = new Date(fechaStr);
                const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
                const anio = fecha.getFullYear().toString().slice(-2);
                return `${mes}/${anio}`;
            },

            async cargarTarjetas() {
                this.cargando = true;
                this.error = null;
                const token = localStorage.getItem('jwt_token');

                try {
                    const response = await fetch('/api/tarjetas', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const json = await response.json();
                        this.tarjetas = json.datos || [];
                    } else {
                        this.tarjetas = [];
                    }
                } catch (e) {
                    this.error = 'Error de conexión';
                    this.tarjetas = [];
                } finally {
                    this.cargando = false;
                }
            }
        }))
    })
</script>
@endpush