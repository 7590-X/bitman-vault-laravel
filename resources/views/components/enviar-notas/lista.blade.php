{{--
    Componente lista de notas seguras enviadas.
--}}
<div class="flex flex-col h-full bg-slate-900 border-r border-slate-700/80 text-slate-100 min-w-l">

    {{-- Encabezado de la lista --}}
    <div class="p-4 border-b border-slate-700/80 flex items-center justify-between shrink-0 bg-slate-900/90 backdrop-blur">
        <div class="flex items-center gap-2.5">
            <x-icon.send-note class="w-5 h-5 text-blue-400" />
            <h3 class="text-base font-bold tracking-tight text-white">Notas Enviadas</h3>
        </div>
        <span class="text-xs px-2 py-1 rounded-full bg-slate-800 border border-slate-700/60 text-slate-300 font-mono" x-text="notas.length">0</span>
    </div>

    {{-- Cuerpo scrolleable de la lista --}}
    <div class="flex-1 overflow-y-auto min-h-0 divide-y divide-slate-800/60">

        {{-- Estado de Carga --}}
        <template x-if="cargando">
            <div class="p-6 text-center text-slate-400 space-y-3">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-slate-600 border-t-blue-500"></div>
                <p class="text-xs font-medium">Cargando notas enviadas...</p>
            </div>
        </template>

        {{-- Estado Vacío --}}
        <template x-if="!cargando && notas.length === 0">
            <div class="p-8 text-center flex flex-col items-center justify-center h-full min-h-[220px]">
                <div class="w-12 h-12 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-slate-400 mb-3 shadow-inner">
                    <x-icon.send-note class="w-6 h-6 opacity-60" />
                </div>
                <h4 class="text-sm font-semibold text-slate-200">Sin Notas Enviadas</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-[200px]">No has enviado ninguna nota segura aún.</p>
            </div>
        </template>

        {{-- Renderizado dinámico de la lista de notas enviadas --}}
        <template x-if="!cargando && notas.length > 0">
            <div class="divide-y divide-slate-800/50">
                <template x-for="nota in notas" :key="nota.id">
                    <x-enviar-notas.item />
                </template>
            </div>
        </template>
    </div>

    {{-- Pie inferior con botón para crear --}}
    <div class="p-3 bg-slate-900 border-t border-slate-700/80 shrink-0">
        <button
            type="button"
            @click="iniciarCreacion()"
            :disabled="creando"
            class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-medium text-sm rounded-lg transition-colors duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Nueva Nota Segura</span>
        </button>
    </div>

</div>