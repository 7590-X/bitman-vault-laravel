{{--
    Componente para visualizar los detalles de una tarjeta seleccionada.
    Diseñado con el mismo formato físico de tarjeta de crédito + campos con ocultar/copiar.
--}}
<div x-data="detalleTarjetaComponent" class="bg-slate-800/80 border border-slate-700/60 rounded-3xl p-6 shadow-2xl relative overflow-hidden space-y-6">

    {{-- Header del Panel --}}
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-bold text-white flex items-center gap-2">
            <x-icon.credit-card class="w-6 h-6 text-blue-400" />
            Detalles de Tarjeta
        </h3>
        <button type="button" @click="$dispatch('cerrar-detalle')" class="text-slate-400 hover:text-white transition-colors">
            <x-icon.x class="w-5 h-5" />
        </button>
    </div>

    {{-- Vista Visual de Tarjeta Física --}}
    <div class="relative w-full aspect-[1.586/1] bg-gradient-to-br from-slate-700 to-slate-900 rounded-2xl border border-slate-600 shadow-xl overflow-hidden p-5 sm:p-6 flex flex-col justify-between">
        <div class="absolute top-0 left-0 w-full h-full opacity-10 mix-blend-overlay pointer-events-none"></div>

        {{-- Encabezado de la Tarjeta --}}
        <div class="flex justify-between items-start z-10">
            <div>
                <h4 class="text-lg font-semibold text-white tracking-wide truncate pr-4" x-text="tarjetaSeleccionada.alias || 'Tarjeta'"></h4>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-slate-400 uppercase tracking-wider" x-text="tarjetaSeleccionada.banco_emisor || 'Banco'"></p>
                    <span x-show="franquiciaDetectada.clave !== 'desconocida'"
                        x-text="franquiciaDetectada.nombre"
                        class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/40 uppercase tracking-wider"></span>
                </div>
            </div>
            <div class="shrink-0 text-slate-300">
                <x-icon.chip class="w-10 h-10 opacity-80" />
            </div>
        </div>

        {{-- Número de Tarjeta Resumido en la Tarjeta Física --}}
        <div class="z-10 mt-4">
            <span class="text-[10px] text-slate-400 uppercase tracking-widest block mb-1">Número de Tarjeta</span>
            <div class="flex items-center gap-3 text-slate-300 font-mono text-lg sm:text-xl tracking-widest">
                <span>••••</span>
                <span>••••</span>
                <span>••••</span>
                <span class="text-white font-semibold" x-text="tarjetaSeleccionada.ultimos_4_digitos || '••••'"></span>
            </div>
        </div>

        {{-- Footer Tarjeta: Titular y Expiración --}}
        <div class="flex justify-between items-end z-10 mt-4">
            <div class="flex flex-col">
                <span class="text-[10px] text-slate-400 uppercase tracking-widest">Titular</span>
                <span class="text-sm text-slate-200 font-medium tracking-wide truncate max-w-[180px]" x-text="tarjetaSeleccionada.nombre_titular || '—'"></span>
            </div>
            <div class="flex flex-col items-end">
                <span class="text-[10px] text-slate-400 uppercase tracking-widest">Expira</span>
                <span class="text-sm text-slate-200 font-medium tracking-wide" x-text="formatFecha(tarjetaSeleccionada.fecha_expiracion)"></span>
            </div>
        </div>
    </div>

    {{-- Campos de Detalle Seguros con Ocultar / Copiar --}}
    <div class="space-y-3 pt-2">
        {{-- Número Completo de Tarjeta (Oculto + Copiable + Ver/Ocultar) --}}
        <x-share.campo-detalle etiqueta="Número Completo de Tarjeta" :oculto="true" :copiable="true">
            <span x-text="numeroDesencriptado || '—'"></span>
        </x-share.campo-detalle>

        {{-- Verificación de Algoritmo Luhn --}}
        <x-share.campo-detalle etiqueta="Verificación">
            <template x-if="esLuhnValido === true">
                <span class="text-xs font-semibold text-emerald-400 flex items-center gap-1.5">
                    <x-icon.check class="w-4 h-4 shrink-0" /> Número Válido
                </span>
            </template>
            <template x-if="esLuhnValido === false">
                <span class="text-xs font-semibold text-red-400 flex items-center gap-1.5">
                    <x-icon.x class="w-4 h-4 shrink-0" /> Número Inválido (Algoritmo de Luhn)
                </span>
            </template>
            <template x-if="esLuhnValido === null">
                <span class="text-xs text-slate-400">Sin verificar</span>
            </template>
        </x-share.campo-detalle>

        {{-- Código CVV (Oculto + Copiable + Ver/Ocultar) --}}
        <x-share.campo-detalle etiqueta="Código de Seguridad (CVV)" :oculto="true" :copiable="true">
            <span x-text="atob(tarjetaSeleccionada.cvv_encriptado)"></span>
        </x-share.campo-detalle>
    </div>

    {{-- Acciones del Panel --}}
    <div class="pt-2 flex gap-3">
        <x-share.button variante="secondary" class="flex-1" @click="$dispatch('cerrar-detalle')">
            Cerrar
        </x-share.button>
        <x-share.button variante="danger" class="flex-1" @click=" confirmarEliminacion()">
            Eliminar
        </x-share.button>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('detalleTarjetaComponent', () => ({
            get numeroDesencriptado() {
                if (!this.tarjetaSeleccionada?.numero_encriptado) return '';
                try {
                    return atob(this.tarjetaSeleccionada.numero_encriptado);
                } catch (e) {
                    return '';
                }
            },
            get franquiciaDetectada() {
                if (window.CardValidator && this.numeroDesencriptado) {
                    return window.CardValidator.detectarFranquicia(this.numeroDesencriptado);
                }
                return {
                    clave: 'desconocida',
                    nombre: 'Tarjeta'
                };
            },
            get esLuhnValido() {
                if (window.CardValidator && this.numeroDesencriptado) {
                    return window.CardValidator.validarLuhn(this.numeroDesencriptado);
                }
                return null;
            },
            formatFecha(fechaStr) {
                if (!fechaStr) return 'MM/YY';
                const fecha = new Date(fechaStr);
                const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
                const anio = fecha.getFullYear().toString().slice(-2);
                return `${mes}/${anio}`;
            },
            async confirmarEliminacion() {
                const result = await window.Dialog.confirm({
                    title: `¿Eliminar ${this.tarjetaSeleccionada?.alias}?`,
                    text: '¿Estás seguro de que deseas eliminar la tarjeta? Esta acción no se puede deshacer y los datos se perderán de forma permanente.',
                    confirmText: 'Sí, eliminar',
                    type: 'danger'
                });

                if (result.isConfirmed) {
                    this.eliminarTarjeta();
                }
            },
            async eliminarTarjeta() {
                const token = localStorage.getItem('jwt_token');
                try {
                    const response = await fetch(`/api/tarjetas/${this.tarjetaSeleccionada.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok || response.status === 200 || response.status === 204) {
                        window.toastr.info('Tarjeta eliminado correctamente', 'Eliminado');
                        this.$dispatch('tarjeta-eliminada', this.tarjetaSeleccionada.id);
                        this.loginSeleccionado = null;
                    } else {
                        window.toastr.error('No se pudo eliminar la tarjeta', 'Error')
                    }
                } catch (e) {
                    window.toastr.error(e.message, 'Error')
                }
            }
        }))
    })
</script>
@endpush