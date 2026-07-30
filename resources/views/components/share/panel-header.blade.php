{{--
    Componente reutilizable de encabezado para paneles del dashboard.
    Estandariza la barra superior: ícono + título + subtítulo.

    Uso con props estáticas (texto fijo):
        <x-share.panel-header titulo="Mi Título" subtitulo="Descripción">
            <x-slot:icono><x-icon.globe class="w-5 h-5" /></x-slot:icono>
        </x-share.panel-header>

    Uso con slots dinámicos (Alpine x-text, etc.):
        <x-share.panel-header>
            <x-slot:icono>...</x-slot:icono>
            <x-slot:titulo><span x-text="variable"></span></x-slot:titulo>
            <x-slot:subtitulo><span x-text="otraVariable"></span></x-slot:subtitulo>
        </x-share.panel-header>

    Slots opcionales en $slot (default): acciones a la derecha del encabezado.

    Props:
      - titulo    : string — Título estático (ignorado si se provee x-slot:titulo)
      - subtitulo : string — Subtítulo estático (ignorado si se provee x-slot:subtitulo)
--}}
@props([
'titulo' => null,
'subtitulo' => null,
])

<div {{ $attributes->merge([
    'class' => 'shrink-0 mb-3 border-b border-slate-700/80 bg-slate-950 flex items-center gap-4'
]) }}>

    {{-- Ícono (slot opcional) --}}
    @if(isset($icono))
    <div class="p-3 min-w-[2.5rem] aspect-square rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-400 shrink-0">
        {{ $icono }}
    </div>
    @endif

    {{-- Textos: Título + Subtítulo (props o slots dinámicos) --}}
    <div class="min-w-0 flex-1">
        <h3 class="text-base font-bold text-white tracking-tight leading-tight truncate">
            @if(isset($titulo) && !is_string($titulo))
            {{-- Slot dinámico (Alpine x-text, etc.) --}}
            {{ $titulo }}
            @else
            {{ $titulo }}
            @endif
        </h3>
        @if(isset($subtitulo) || $subtitulo)
        <p class="text-xs text-slate-400 mt-0.5 truncate">
            @if(isset($subtitulo) && !is_string($subtitulo))
            {{ $subtitulo }}
            @else
            {{ $subtitulo }}
            @endif
        </p>
        @endif
    </div>

    {{-- Acciones adicionales a la derecha (default slot) --}}
    @if(!$slot->isEmpty())
    <div class="shrink-0 flex items-center gap-2">
        {{ $slot }}
    </div>
    @endif

</div>