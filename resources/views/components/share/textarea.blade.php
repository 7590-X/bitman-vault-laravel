{{--
    Componente de Textarea reutilizable.
    Delega label, wrapper y error a <x-share.form-field>.
    Sólo define el elemento <textarea> y sus props específicas.

    Props:
      - nombre      : string — ID / name del campo
      - etiqueta    : string — Label del campo
      - placeholder : string — Placeholder del campo
      - requerido   : bool   — Muestra el indicador * en el label
      - filas       : int    — Número de filas del textarea [default: 3]
      - errorKey    : string — Clave en errores[] de Alpine (default: nombre)

    Attributes:
      Todos los atributos extra (x-model, @input, etc.) se aplican directamente al <textarea>.
--}}
@props([
    'nombre'      => '',
    'etiqueta'    => '',
    'placeholder' => '',
    'requerido'   => false,
    'filas'       => 3,
    'errorKey'    => null,
])

@php
    $key = $errorKey ?? $nombre;
@endphp

<x-share.form-field
    :nombre="$nombre"
    :etiqueta="$etiqueta"
    :requerido="$requerido"
    :error-key="$key"
>
    <textarea
        @if($nombre) id="{{ $nombre }}" name="{{ $nombre }}" @endif
        rows="{{ $filas }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'block w-full rounded-lg border bg-slate-900 text-slate-100 text-sm px-4 py-2.5 placeholder:text-slate-600 focus:outline-none focus:ring-2 transition-all duration-150 resize-none'
        ]) }}
        :class="errores['{{ $key }}']
            ? 'border-red-500 focus:border-red-400 focus:ring-red-500/20'
            : 'border-slate-600 hover:border-slate-500 focus:border-blue-500 focus:ring-blue-500/20'"
    ></textarea>
</x-share.form-field>