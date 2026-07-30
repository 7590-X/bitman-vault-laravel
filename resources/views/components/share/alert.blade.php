{{--
    Componente de Alerta reutilizable (Error, Éxito, Advertencia).
    Props:
      - tipo: 'error' | 'success' | 'warning' | 'info' (default: 'error')
      - mensaje: string (opcional, o usar el slot)
--}}
@props([
    'tipo'    => 'error',
    'mensaje' => null,
])

@php
    $clases = match($tipo) {
        'error'   => 'bg-red-500/10 border-red-500/30 text-red-400',
        'success' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
        'warning' => 'bg-amber-500/10 border-amber-500/30 text-amber-400',
        'info'    => 'bg-blue-500/10 border-blue-500/30 text-blue-400',
        default   => 'bg-red-500/10 border-red-500/30 text-red-400',
    };
@endphp

<div {{ $attributes->merge([
    'class' => "p-3.5 rounded-lg border text-xs font-medium flex items-center gap-2.5 shadow-sm {$clases}"
]) }}>
    <svg class="w-4 h-4 shrink-0 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        @if($tipo === 'success')
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        @else
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        @endif
    </svg>
    <div>
        {{ $mensaje ?? $slot }}
    </div>
</div>
