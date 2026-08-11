<!-- ESTADO 2: LECTURA DE NOTA Y TEMPORIZADOR DE AUTODESTRUCCIÓN -->
<div x-show="paso === 'lectura'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden">

    <!-- Barra de Progreso del Temporizador -->
    <div class="absolute top-0 left-0 h-1.5 bg-gradient-to-r from-emerald-500 via-amber-500 to-rose-600 transition-all duration-1000 ease-linear"
        :style="`width: ${porcentajeProgreso}%`"></div>

    <!-- Encabezado del Mensaje y Reloj -->
    <div class="flex items-center justify-between pb-4 border-b mb-1 pb-2 border-slate-800 mb-6">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Nota Aperturada - Lectura Activa</span>
            </div>
            <h2 class="text-lg font-bold text-white truncate" x-text="notaDatos ? notaDatos.titulo : 'Nota Secreta'"></h2>
        </div>

        <!-- Reloj Digital y Anillo de Progreso -->
        <div class="shrink-0 ml-4 flex items-center gap-3 bg-slate-950/80 px-4 py-2 rounded-xl border border-slate-800 shadow-inner">
            <div class="text-right">
                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Autodestrucción en</div>
                <div class="font-mono text-xl font-extrabold text-amber-400" x-text="`${tiempoRestante}s`">30s</div>
            </div>
            <div class="relative w-8 h-8 flex items-center justify-center">
                <svg class="w-8 h-8 transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    <path class="text-amber-400 transition-all duration-1000 ease-linear" stroke-width="3" :stroke-dasharray="`${porcentajeProgreso}, 100`" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Caja de Texto Confidencial -->
    <div class="relative mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Contenido Secreto</span>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="mostrarContenido = !mostrarContenido"
                    class="text-xs text-slate-400 hover:text-slate-200 flex items-center gap-1 transition-colors">
                    <span x-text="mostrarContenido ? 'Ocultar' : 'Mostrar'"></span>
                </button>
                <button
                    type="button"
                    @click="copiarContenido()"
                    class="text-xs text-blue-400 hover:text-blue-300 font-medium flex items-center gap-1 transition-colors">
                    <x-icon.copy class="w-3.5 h-3.5" />
                    <span>Copiar Texto</span>
                </button>
            </div>
        </div>

        <div class="p-5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 font-mono text-sm leading-relaxed whitespace-pre-wrap min-h-[140px] max-h-[300px] overflow-y-auto shadow-inner selection:bg-blue-600 selection:text-white"
            :class="!mostrarContenido ? 'filter blur-sm select-none' : ''">
            <span x-text="notaDatos ? notaDatos.contenido : ''"></span>
        </div>
    </div>

    <!-- Acciones del pie de lectura -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-800">
        <p class="text-xs text-slate-400">
            El contenido se borrará al finalizar el tiempo.
        </p>
        <button
            type="button"
            @click="destruirNota()"
            class="px-4 py-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 font-semibold text-xs rounded-xl transition-colors">
            Cerrar y Destruir Ahora
        </button>
    </div>
</div>
