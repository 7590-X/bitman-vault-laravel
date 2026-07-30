{{--
    Componente de Input reutilizable.
    Delega label, wrapper y error a <x-share.form-field>.
    Sólo define el elemento <input> y sus props específicas.

    Props:
      - nombre      : string — ID / name del campo
      - etiqueta    : string — Label del campo
      - tipo        : string — type del input (text, email, url, etc.) [default: text]
      - placeholder : string — Placeholder del campo
      - requerido   : bool   — Muestra el indicador * en el label
      - errorKey    : string — Clave en errores[] de Alpine (default: nombre)
      - ayuda       : string — Texto de ayuda opcional

    Attributes:
      Todos los atributos extra (x-model, @input, etc.) se aplican directamente al <input>.
--}}
@props([
    'nombre'      => '',
    'etiqueta'    => '',
    'tipo'        => 'text',
    'placeholder' => '',
    'requerido'   => false,
    'errorKey'    => null,
    'ayuda'       => null,
])

@php
    $key = $errorKey ?? $nombre;
@endphp

<x-share.form-field
    :nombre="$nombre"
    :etiqueta="$etiqueta"
    :requerido="$requerido"
    :error-key="$key"
    :ayuda="$ayuda"
>
    <input
        @if($nombre) id="{{ $nombre }}" name="{{ $nombre }}" @endif
        type="{{ $tipo }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'block w-full rounded-lg border bg-slate-900 text-slate-100 text-sm px-4 py-2.5 placeholder:text-slate-600 focus:outline-none focus:ring-2 transition-all duration-150 appearance-none'
        ]) }}
        :class="errores['{{ $key }}']
            ? 'border-red-500 focus:border-red-400 focus:ring-red-500/20'
            : 'border-slate-600 hover:border-slate-500 focus:border-blue-500 focus:ring-blue-500/20'"
    />
</x-share.form-field>