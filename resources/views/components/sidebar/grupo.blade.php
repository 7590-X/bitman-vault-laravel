{{--
    Componente de grupo para el menú lateral (ej. Vault, All items).
    Props:
      - titulo: Nombre del grupo
      - icono (opcional): Código SVG o emoji para mostrar al lado del título
      - abierto (opcional): true por defecto, determina si empieza expandido
--}}
@props([
    'titulo',
    'icono' => null,
    'abierto' => true
])

<div x-data="{ open: {{ $abierto ? 'true' : 'false' }} }" class="w-full">
    {{-- Cabecera del grupo (botón de expansión) --}}
    <button 
        @click="open = !open" 
        class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-semibold text-[#a5a9b0] hover:text-white hover:bg-[#2d333b] transition-colors focus:outline-none"
    >
        <div class="flex items-center gap-2.5">
            @if($icono)
                <span class="w-5 h-5 flex items-center justify-center shrink-0">
                    {!! $icono !!}
                </span>
            @endif
            <span>{{ $titulo }}</span>
        </div>
        
        {{-- Chevrón animado --}}
        <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="open ? 'rotate-180' : ''"
            fill="none" viewBox="0 0 24 24" stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Contenido del grupo (items) --}}
    <div 
        x-show="open" 
        x-collapse 
        class="flex flex-col pb-1"
        @style(['display: flex' => $abierto, 'display: none' => !$abierto])
    >
        {{ $slot }}
    </div>
</div>
