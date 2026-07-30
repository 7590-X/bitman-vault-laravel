{{--
    Componente para representar un ítem individual de login en la lista vertical.
--}}
@props([
'nombreSitio' => '',
'usuarioLogin' => '',
'url' => null,
'activo' => false,
])

<div
    {{ $attributes->merge([
        'class' => 'flex items-center gap-3 px-4 py-3 border-b border-slate-800/60 hover:bg-slate-800/80 cursor-pointer transition-colors duration-150 group ' . ($activo ? 'bg-slate-800/90 border-l-4 border-l-blue-500' : '')
    ]) }}>
    {{-- Ícono del sitio / Favicon o Globo por defecto --}}
    <div class="w-5 h-5 rounded-lg bg-slate-800 border border-slate-700/70 flex items-center justify-center shrink-0 text-slate-300 group-hover:border-blue-500/50 group-hover:text-blue-400 transition-colors">
        <x-icon.globe class="w-5 h-5" />
    </div>

    {{-- Título (Nombre del sitio) y Subtítulo (Usuario Login) --}}
    <div class="min-w-0 flex-1">
        <h4 class="text-sm font-semibold text-slate-100 truncate group-hover:text-white transition-colors">
            {{ $nombreSitio }}
        </h4>
        <p class="text-xs text-slate-400 truncate mt-0.5 font-sans">
            {{ $usuarioLogin ?: 'Sin usuario' }}
        </p>
    </div>
</div>