{{--
    Componente de Botón reutilizable con variantes estandarizadas.
    Props:
      - variante: 'primary' | 'secondary' | 'danger' (default: 'primary')
      - tipo: 'button' | 'submit' (default: 'button')
      - cargando: string|bool — nombre de variable Alpine de carga (ej. 'enviando')
--}}
@props([
'variante' => 'primary',
'tipo' => 'button',
'cargando' => null,
])

@php
$clasesVariante = match($variante) {
'primary' => 'bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-semibold shadow-md shadow-blue-900/20 border border-blue-500/40',
'secondary' => 'bg-slate-800 hover:bg-slate-700 active:bg-slate-800 text-slate-300 hover:text-white font-semibold border border-slate-700/80 shadow-sm',
'danger' => 'bg-red-600 hover:bg-red-500 active:bg-red-700 text-white font-semibold shadow-md border border-red-500/40',
default => 'bg-blue-600 hover:bg-blue-500 text-white font-semibold',
};
@endphp

<button
    type="{{ $tipo }}"
    @if($cargando) :disabled="{{ $cargando }}" @endif
    {{ $attributes->merge([
        'class' => "px-5 py-2.5 text-sm rounded-lg transition-all duration-150 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 disabled:opacity-50 disabled:cursor-not-allowed {$clasesVariante}"
    ]) }}>
    @if($cargando)
    <template x-if="{{ $cargando }}">
        <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
    </template>
    @endif
    <span>{{ $slot }}</span>
</button>