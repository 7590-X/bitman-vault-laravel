{{--
    Componente de Password reutilizable con toggle Ver/Ocultar.
    Delega label, wrapper y error a <x-share.form-field>.
    Requiere `mostrarPassword` en el scope Alpine del componente padre.

    Props:
      - nombre      : string — ID / name del campo [default: contrasena]
      - etiqueta    : string — Label del campo [default: Contraseña]
      - placeholder : string — Placeholder del campo
      - requerido   : bool   — Muestra el indicador * en el label
      - errorKey    : string — Clave en errores[] de Alpine (default: nombre)

    Attributes:
      Todos los atributos extra (x-model, etc.) se aplican directamente al <input>.
--}}
@props([
'nombre' => 'contrasena',
'etiqueta' => 'Contraseña',
'placeholder' => '••••••••••••',
'requerido' => false,
'errorKey' => null,
])

@php
$key = $errorKey ?? $nombre;
@endphp

<x-share.form-field
    :nombre="$nombre"
    :etiqueta="$etiqueta"
    :requerido="$requerido"
    :error-key="$key">
    <div class="relative">
        <input
            @if($nombre) id="{{ $nombre }}" name="{{ $nombre }}" @endif
            :type="mostrarPassword ? 'text' : 'password'"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border bg-slate-900 text-slate-100 text-sm pl-4 pr-24 py-2.5 placeholder:text-slate-600 focus:outline-none focus:ring-2 transition-all duration-150 font-mono'
            ]) }}
            :class="errores['{{ $key }}']
                ? 'border-red-500 focus:border-red-400 focus:ring-red-500/20'
                : 'border-slate-600 hover:border-slate-500 focus:border-blue-500 focus:ring-blue-500/20'" />
        <button
            type="button"
            @click="mostrarPassword = !mostrarPassword"
            class="absolute mt-1 inset-y-0 right-0 px-3.5 flex items-center gap-1 text-xs font-semibold text-slate-400 hover:text-slate-100 border-none hover:bg-slate-800/60 transition-colors select-none">
            <x-icon.eye-off x-show="mostrarPassword" class="w-3.5 h-3.5 shrink-0" />
            <x-icon.eye x-show="!mostrarPassword" class="w-3.5 h-3.5 shrink-0" />
            <span x-text="mostrarPassword ? 'Ocultar' : 'Mostrar'"></span>
        </button>
    </div>
</x-share.form-field>