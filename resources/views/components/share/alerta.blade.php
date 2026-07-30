{{--
    Componente de alerta reutilizable.
    Muestra mensajes de éxito, error o información al usuario.
    Props:
      - tipo: 'exito' | 'error' | 'advertencia' (default: 'info')
      - mensaje: string
--}}
@props([
    'tipo'    => 'info',
    'mensaje' => '',
])

@php
    $estilos = match($tipo) {
        'exito'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'error'       => 'bg-red-50 text-red-700 border-red-200',
        'advertencia' => 'bg-amber-50 text-amber-700 border-amber-200',
        default       => 'bg-blue-50 text-blue-700 border-blue-200',
    };

    $icono = match($tipo) {
        'exito'       => '✓',
        'error'       => '✕',
        'advertencia' => '⚠',
        default       => 'ℹ',
    };
@endphp

<div
    role="alert"
    class="flex items-start gap-3 rounded-lg border px-4 py-3 text-sm font-medium {{ $estilos }}"
>
    <span class="mt-0.5 shrink-0 font-bold" aria-hidden="true">{{ $icono }}</span>
    <p>{{ $mensaje }}</p>
</div>
