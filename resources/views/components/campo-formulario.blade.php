{{--
    Componente de campo de formulario reutilizable.
    Renderiza label, input y mensaje de error de validación.
    Props:
      - nombre: string — nombre del campo (name/id del input)
      - etiqueta: string — texto del label
      - tipo: string — tipo del input (default: 'text')
      - valor: string — valor inicial del campo
      - requerido: bool — si el campo es obligatorio
      - placeholder: string — texto placeholder del input
--}}
@props([
    'nombre'      => '',
    'etiqueta'    => '',
    'tipo'        => 'text',
    'valor'       => '',
    'requerido'   => false,
    'placeholder' => '',
])

<div class="flex flex-col gap-1">
    <label
        for="{{ $nombre }}"
        class="text-sm font-medium text-slate-700"
    >
        {{ $etiqueta }}
        @if($requerido)
            <span class="text-red-500 ml-0.5" aria-hidden="true">*</span>
        @endif
    </label>

    <input
        id="{{ $nombre }}"
        name="{{ $nombre }}"
        type="{{ $tipo }}"
        value="{{ old($nombre, $valor) }}"
        placeholder="{{ $placeholder }}"
        {{ $requerido ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full rounded-md border border-slate-300 bg-slate-100
                        px-3 py-2 text-sm text-slate-700
                        placeholder:text-slate-500
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500
                        focus:outline-none transition-colors duration-150
                        ' . ($errors->has($nombre) ? 'border-red-500 bg-red-50' : '')
        ]) }}
    >

    @error($nombre)
        <p class="text-xs font-medium text-red-700 mt-1" role="alert">
            {{ $message }}
        </p>
    @enderror
</div>
