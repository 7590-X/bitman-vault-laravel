{{--
    Componente de Badge / Etiqueta de Estado reutilizable.
    Props:
      - variante: 'amber'|'warning'|'emerald'|'success'|'rose'|'danger'|'blue'|'info'|'slate' (default: 'info')
      - estado: string|null — Expresión Alpine.js del objeto/variable de estado (ej: 'notaSeleccionada.estado')
      - etiqueta: string|null — Expresión Alpine.js de la etiqueta (ej: 'notaSeleccionada.estado_etiqueta')
      - indicador: bool — Si muestra el círculo indicador de estado (default: true)
--}}
@props([
'variante' => 'info',
'estado' => null,
'etiqueta' => null,
'indicador' => true,
])

@php
$clasesEstaticas = match($variante) {
'amber', 'warning', 'no_aperturada' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
'emerald', 'success', 'aperturada' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
'rose', 'danger', 'expirada', 'revocada' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
'blue', 'info' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
'slate', 'secondary' => 'bg-slate-800 text-slate-300 border-slate-700/80',
default => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
};

$clasesPuntoEstaticas = match($variante) {
'amber', 'warning', 'no_aperturada' => 'bg-amber-400',
'emerald', 'success', 'aperturada' => 'bg-emerald-400',
'rose', 'danger', 'expirada', 'revocada' => 'bg-rose-400',
'blue', 'info' => 'bg-blue-400',
'slate', 'secondary' => 'bg-slate-400',
default => 'bg-blue-400',
};
@endphp

<span
    {{ $attributes->merge([
        'class' => "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border transition-colors {$clasesEstaticas}"
    ]) }}
    @if($estado)
    :class="{
        'bg-amber-500/10 text-amber-400 border-amber-500/30': {{ $estado }} === 'no_aperturada' || {{ $estado }} === 'warning' || {{ $estado }} === 'amber',
        'bg-emerald-500/10 text-emerald-400 border-emerald-500/30': {{ $estado }} === 'aperturada' || {{ $estado }} === 'success' || {{ $estado }} === 'emerald',
        'bg-rose-500/10 text-rose-400 border-rose-500/30': {{ $estado }} === 'expirada' || {{ $estado }} === 'revocada' || {{ $estado }} === 'danger' || {{ $estado }} === 'rose',
        'bg-blue-500/10 text-blue-400 border-blue-500/30': {{ $estado }} === 'info' || {{ $estado }} === 'blue',
        'bg-slate-800 text-slate-300 border-slate-700/80': {{ $estado }} === 'secondary' || {{ $estado }} === 'slate'
    }"
    @endif>
    @if($indicador)
    <span
        class="px-2 py-0.5 w-1.5 h-1.5 rounded-full mr-1.5 {{ $clasesPuntoEstaticas }}"
        @if($estado)
        :class="{
            'bg-amber-400': {{ $estado }} === 'no_aperturada' || {{ $estado }} === 'warning' || {{ $estado }} === 'amber',
            'bg-emerald-400': {{ $estado }} === 'aperturada' || {{ $estado }} === 'success' || {{ $estado }} === 'emerald',
            'bg-rose-400': {{ $estado }} === 'expirada' || {{ $estado }} === 'revocada' || {{ $estado }} === 'danger' || {{ $estado }} === 'rose',
            'bg-blue-400': {{ $estado }} === 'info' || {{ $estado }} === 'blue',
            'bg-slate-400': {{ $estado }} === 'secondary' || {{ $estado }} === 'slate'
        }"
        @endif></span>
    @endif

    @if($etiqueta)
    <span class="px-2 py-0.5 text-[10px] shrink-0 font-medium" x-text="{{ $etiqueta }}"></span>
    @else
    {{ $slot }}
    @endif
</span>