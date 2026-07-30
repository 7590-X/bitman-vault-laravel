{{--
    Componente de Tarjeta / Contenedor reutilizable con bordes de alto contraste.
--}}
@props([
'titulo' => null,
'subtitulo' => null,
'icono' => null,
])

<div {{ $attributes->merge([
    'class' => 'w-full bg-slate-900/95 border border-slate-700/80 rounded-xl shadow-2xl overflow-hidden flex flex-col backdrop-blur-md'
]) }}>
    @if($titulo || isset($header))
    <div class="px-6 py-5 border-b border-slate-700/80 bg-slate-900/90 flex items-center justify-between shrink-0">
        @if(isset($header))
        {{ $header }}
        @else
        <div class="flex items-center gap-3">
            @if($icono)
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shadow-sm">
                {!! $icono !!}
            </div>
            @endif
            <div>
                <h3 class="text-lg font-bold text-white tracking-tight">{{ $titulo }}</h3>
                @if($subtitulo)
                <p class="text-xs text-slate-400 mt-0.5">{{ $subtitulo }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="flex-1 overflow-y-auto">
        {{ $slot }}
    </div>

    @if(isset($footer))
    <div class="px-6 py-4 border-t border-slate-700/80 bg-slate-900/90 flex items-center justify-end gap-3 shrink-0">
        {{ $footer }}
    </div>
    @endif
</div>