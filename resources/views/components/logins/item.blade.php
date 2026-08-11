{{--
    Componente reutilizable para representar un ítem individual de Login en la lista.
--}}
<div
    @click="seleccionarLogin(login)"
    class="flex items-center gap-3 px-4 py-3 border-b border-slate-800/60 transition-colors duration-150 group"
    :class="[
        loginSeleccionado && loginSeleccionado.id === login.id && !creando ? 'bg-slate-800 border-l-4 border-l-blue-500 font-semibold' : '',
        creando ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-800/80 cursor-pointer'
    ]">
    {{-- Icono --}}
    <div class="w-9 h-9 rounded-lg bg-slate-800 border border-slate-700/70 flex items-center justify-center shrink-0 text-slate-300 group-hover:border-blue-500/50 group-hover:text-blue-400 transition-colors">
        <x-icon.globe class="w-5 h-5" />
    </div>

    {{-- Título y Subtítulo --}}
    <div class="min-w-0 flex-1">
        <h4 class="text-sm font-semibold text-slate-100 truncate group-hover:text-white transition-colors" x-text="login.nombre_sitio"></h4>
        <p class="text-xs text-slate-400 truncate mt-0.5 font-sans" x-text="login.usuario_login || 'Sin usuario'"></p>
    </div>
</div>