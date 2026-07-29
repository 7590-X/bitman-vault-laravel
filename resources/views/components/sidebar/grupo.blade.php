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
        class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 transition-colors duration-200 focus:outline-none rounded-md mx-2 my-0.5"
    >
        <div class="flex items-center gap-2.5">
            @if(isset($iconoSlot) && $iconoSlot->isNotEmpty())
                <span class="w-5 h-5 flex items-center justify-center shrink-0 text-slate-300">
                    {{ $iconoSlot }}
                </span>
            @elseif($icono)
                <span class="w-5 h-5 flex items-center justify-center shrink-0 text-slate-300">
                    @if(is_string($icono) && view()->exists("components.icon.{$icono}"))
                        <x-dynamic-component :component="'icon.' . $icono" class="w-5 h-5" />
                    @else
                        {!! $icono !!}
                    @endif
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
