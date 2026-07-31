{{--
    Componente: Formulario para crear una nueva tarjeta.
    Diseñado con formato físico de tarjeta de crédito para una mejor UX.
--}}
<div x-data="crearTarjetaForm" class="bg-slate-800/80 border border-slate-700/60 rounded-3xl p-6 shadow-2xl relative overflow-hidden">

    {{-- Efectos de fondo del formulario --}}
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-white flex items-center gap-2">
            <x-icon.credit-card class="w-6 h-6 text-blue-400" />
            Nueva Tarjeta
        </h3>
        <button type="button" @click="$dispatch('cancelar-creacion')" class="text-slate-400 hover:text-white transition-colors">
            <x-icon.x class="w-5 h-5" />
        </button>
    </div>

    <form @submit.prevent="guardar" class="space-y-6 relative z-10">

        {{-- Tarjeta interactiva (Formulario incrustado en el diseño de tarjeta) --}}
        <div class="relative w-full aspect-[1.586/1] bg-gradient-to-br from-slate-700 to-slate-900 rounded-2xl border border-slate-600 shadow-xl overflow-hidden p-5 sm:p-6 flex flex-col justify-between">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 mix-blend-overlay pointer-events-none"></div>

            {{-- Encabezado de la Tarjeta --}}
            <div class="flex justify-between items-start">
                <div class="w-full mr-4">
                    <label class="sr-only">Alias / Nombre de la Tarjeta</label>
                    <input type="text" x-model="formulario.alias" placeholder="Alias (Ej. Mi Tarjeta Platinum)" required maxlength="30"
                        class="w-full bg-transparent border-none text-white text-lg font-semibold placeholder:text-slate-400 focus:ring-0 p-0 focus:outline-none">
                    <label class="sr-only">Banco Emisor</label>
                    <input type="text" x-model="formulario.banco_emisor" placeholder="Banco Emisor" maxlength="30"
                        class="w-full bg-transparent border-none text-slate-300 text-xs uppercase tracking-wider placeholder:text-slate-500 focus:ring-0 p-0 mt-1 focus:outline-none">
                </div>
                <div class="shrink-0 text-slate-300">
                    <x-icon.chip class="w-10 h-10 opacity-80" />
                </div>
            </div>

            {{-- Número de Tarjeta --}}
            <div class="mt-4">
                <label class="text-[10px] text-slate-400 uppercase tracking-widest mb-1 block">Número de Tarjeta</label>
                <div class="flex gap-2">
                    <input type="text" x-model="formulario.numero_parte1" @input="formatNumber($event, 4, 'numero_parte2')" maxlength="4" placeholder="••••" required class="w-1/4 bg-slate-800/50 border border-slate-600/50 rounded-md text-center text-white font-mono text-lg tracking-widest focus:ring-1 focus:ring-blue-500 focus:border-blue-500 px-1 py-1.5 transition-colors">
                    <input type="text" x-model="formulario.numero_parte2" x-ref="numero_parte2" @input="formatNumber($event, 4, 'numero_parte3')" maxlength="4" placeholder="••••" required class="w-1/4 bg-slate-800/50 border border-slate-600/50 rounded-md text-center text-white font-mono text-lg tracking-widest focus:ring-1 focus:ring-blue-500 focus:border-blue-500 px-1 py-1.5 transition-colors">
                    <input type="text" x-model="formulario.numero_parte3" x-ref="numero_parte3" @input="formatNumber($event, 4, 'numero_parte4')" maxlength="4" placeholder="••••" required class="w-1/4 bg-slate-800/50 border border-slate-600/50 rounded-md text-center text-white font-mono text-lg tracking-widest focus:ring-1 focus:ring-blue-500 focus:border-blue-500 px-1 py-1.5 transition-colors">
                    <input type="text" x-model="formulario.numero_parte4" x-ref="numero_parte4" maxlength="4" placeholder="••••" required class="w-1/4 bg-slate-800/50 border border-slate-600/50 rounded-md text-center text-white font-mono text-lg tracking-widest focus:ring-1 focus:ring-blue-500 focus:border-blue-500 px-1 py-1.5 transition-colors">
                </div>
            </div>

            {{-- Footer Tarjeta: Titular, Fecha, CVV --}}
            <div class="flex justify-between items-end gap-3 mt-4">
                <div class="flex-1">
                    <label class="text-[10px] text-slate-400 uppercase tracking-widest block mb-1">Titular</label>
                    <input type="text" x-model="formulario.nombre_titular" placeholder="NOMBRE APELLIDO" required maxlength="150"
                        class="w-full bg-slate-800/50 border border-slate-600/50 rounded-md text-white text-sm font-medium uppercase tracking-wide placeholder:text-slate-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 px-2 py-1.5">
                </div>
                <div class="w-20 shrink-0">
                    <label class="text-[10px] text-slate-400 uppercase tracking-widest block mb-1">Expira</label>
                    <input type="text" x-model="formulario.fecha_expiracion_visual" @input="formatFecha" maxlength="5" placeholder="MM/YY" required
                        class="w-full bg-slate-800/50 border border-slate-600/50 rounded-md text-white text-sm font-medium tracking-wide placeholder:text-slate-500 text-center focus:ring-1 focus:ring-blue-500 focus:border-blue-500 px-1 py-1.5">
                </div>
                <div class="w-16 shrink-0">
                    <label class="text-[10px] text-slate-400 uppercase tracking-widest block mb-1">CVV</label>
                    <input type="password" x-model="formulario.cvv" maxlength="4" placeholder="•••" required
                        class="w-full bg-slate-800/50 border border-slate-600/50 rounded-md text-white text-sm font-mono tracking-widest placeholder:text-slate-500 text-center focus:ring-1 focus:ring-blue-500 focus:border-blue-500 px-1 py-1.5">
                </div>
            </div>
        </div>

        {{-- Controles Adicionales (Tipo de tarjeta) --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Tipo de Tarjeta</label>
            <select x-model="formulario.tipo_tarjeta_id" required class="w-full bg-slate-900 border border-slate-700 rounded-lg text-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                <option value="1">Crédito</option>
                <option value="2">Débito</option>
            </select>
        </div>

        {{-- Errores --}}
        <template x-if="error">
            <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                <p class="text-sm text-red-400" x-text="error"></p>
            </div>
        </template>

        {{-- Acciones --}}
        <div class="pt-2 flex gap-3">
            <x-share.button variante="secondary" class="flex-1" @click="$dispatch('cancelar-creacion')">
                Cancelar
            </x-share.button>
            <x-share.button tipo="submit" variante="primary" class="flex-1" cargando="guardando">
                <span x-text="guardando ? 'Guardando...' : 'Guardar Tarjeta'"></span>
            </x-share.button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('crearTarjetaForm', () => ({
            guardando: false,
            error: null,
            formulario: {
                tipo_tarjeta_id: 1,
                alias: '',
                numero_parte1: '',
                numero_parte2: '',
                numero_parte3: '',
                numero_parte4: '',
                nombre_titular: '',
                fecha_expiracion_visual: '',
                cvv: '',
                banco_emisor: ''
            },

            formatNumber(e, maxLen, nextRefName) {
                let val = e.target.value.replace(/\D/g, '');
                e.target.value = val;
                this.formulario[e.target.dataset.model] = val; // Sync model si necesario (alpine maneja x-model usualmente, esto es fallback)

                if (val.length === maxLen && nextRefName && this.$refs[nextRefName]) {
                    this.$refs[nextRefName].focus();
                }
            },

            formatFecha(e) {
                let val = e.target.value.replace(/\D/g, '');
                if (val.length > 2) {
                    val = val.substring(0, 2) + '/' + val.substring(2, 4);
                }
                e.target.value = val;
                this.formulario.fecha_expiracion_visual = val;
            },

            async guardar() {
                this.guardando = true;
                this.error = null;
                const token = localStorage.getItem('jwt_token');

                // Procesar datos para el payload
                const numeroCompleto = `${this.formulario.numero_parte1}${this.formulario.numero_parte2}${this.formulario.numero_parte3}${this.formulario.numero_parte4}`;
                const ultimos4 = this.formulario.numero_parte4.padStart(4, '*').slice(-4);

                // Procesar fecha MM/YY -> YYYY-MM-DD
                let fechaSql = null;
                if (this.formulario.fecha_expiracion_visual.length === 5) {
                    const [mes, anio] = this.formulario.fecha_expiracion_visual.split('/');
                    fechaSql = `20${anio}-${mes}-01`;
                }

                const payload = {
                    tipo_tarjeta_id: parseInt(this.formulario.tipo_tarjeta_id),
                    alias: this.formulario.alias,
                    numero_encriptado: btoa(numeroCompleto), // Encriptación simulada básica para envío
                    ultimos_4_digitos: ultimos4,
                    nombre_titular: this.formulario.nombre_titular.toUpperCase(),
                    fecha_expiracion: fechaSql,
                    cvv_encriptado: btoa(this.formulario.cvv),
                    banco_emisor: this.formulario.banco_emisor
                };

                try {
                    const response = await fetch('/api/tarjetas', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    if (response.ok) {
                        const json = await response.json();
                        // Reiniciar formulario
                        this.formulario = {
                            tipo_tarjeta_id: 1,
                            alias: '',
                            numero_parte1: '',
                            numero_parte2: '',
                            numero_parte3: '',
                            numero_parte4: '',
                            nombre_titular: '',
                            fecha_expiracion_visual: '',
                            cvv: '',
                            banco_emisor: ''
                        };

                        // Despachar evento para que el index actualice
                        this.$dispatch('tarjeta-creada', json.datos);
                    } else {
                        const errorData = await response.json();
                        this.error = errorData.message || 'Error al guardar la tarjeta. Verifica los datos.';
                    }
                } catch (e) {
                    this.error = 'Error de conexión con el servidor.';
                } finally {
                    this.guardando = false;
                }
            }
        }));
    });
</script>
@endpush