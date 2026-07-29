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
    class="flex items-center gap-2.5 py-2 pr-3 {{ $paddingIzq }} text-sm font-medium text-[#c0c4cc] hover:text-white hover:bg-[#2d333b] transition-colors rounded-md mx-2 my-0.5"
    :class="currentRoute === '{{ $href }}' ? 'bg-blue-600/10 text-blue-400 font-semibold' : ''"
>
    @if($icono)
        <span class="w-4 h-4 flex items-center justify-center shrink-0 opacity-70">
            {!! $icono !!}
        </span>
    @endif
    <span>{{ $titulo }}</span>
</a>
