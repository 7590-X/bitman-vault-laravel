{{--
    Componente de ítem individual para la barra lateral.
    Props:
      - href: URL (generalmente un hash para SPA, ej: #/logins)
      - titulo: Nombre del ítem
      - icono (opcional): Código SVG o emoji
      - subitem (opcional): booleano, si es true agrega más padding izquierdo
--}}
@props([
    'href' => '#',
    'titulo',
    'icono' => null,
    'subitem' => true
])

@php
    $paddingIzq = $subitem ? 'pl-9' : 'pl-3';
@endphp

<a 
    href="{{ $href }}"
    class="flex items-center gap-2.5 py-2 pr-3 {{ $paddingIzq }} text-sm font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 transition-colors duration-200 rounded-md mx-2 my-0.5"
    :class="currentRoute === '{{ $href }}' ? 'bg-blue-500/10 text-blue-400 font-semibold' : ''"
>
    @if(isset($iconoSlot) && $iconoSlot->isNotEmpty())
        <span class="w-4 h-4 flex items-center justify-center shrink-0 opacity-80">
            {{ $iconoSlot }}
        </span>
    @elseif($icono)
        <span class="w-4 h-4 flex items-center justify-center shrink-0 opacity-80">
            @if(is_string($icono) && view()->exists("components.icon.{$icono}"))
                <x-dynamic-component :component="'icon.' . $icono" class="w-4 h-4" />
            @else
                {!! $icono !!}
            @endif
        </span>
    @endif
    <span>{{ $titulo }}</span>
</a>
