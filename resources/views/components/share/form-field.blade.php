{{--
    Componente base para campos de formulario.
    Encapsula el patrón repetido: wrapper + label + slot + mensaje de error.

    Todos los componentes de formulario (input, password, textarea) deben usar
    este componente internamente para garantizar consistencia visual y comportamiento.

    Props:
      - nombre    : string — ID / name del campo (para el `for` del label)
      - etiqueta  : string — Texto de la etiqueta (label)
      - requerido : bool   — Si true, muestra el indicador * azul
      - errorKey  : string — Clave en el objeto `errores` de Alpine (default: valor de `nombre`)
      - ayuda     : string — Texto de ayuda opcional debajo del campo

    Slot (default):
      El elemento de campo en sí (input, textarea, div relativo para password, etc.)
--}}
@props([
'nombre' => '',
'etiqueta' => '',
'requerido' => false,
'errorKey' => null,
'ayuda' => null,
])

@php
$key = $errorKey ?? $nombre;
@endphp

<div class="flex flex-col gap-1.5 w-full mb-2 p-1">

    {{-- ─── Etiqueta ───────────────────────────────────────────── --}}
    @if($etiqueta)
    <label
        @if($nombre) for="{{ $nombre }}" @endif
        class="text-xs font-semibold uppercase tracking-widest text-slate-400 select-none leading-none">
        {{ $etiqueta }}
        @if($requerido)
        <span class="text-blue-400 font-bold ml-0.5">*</span>
        @endif
    </label>
    @endif

    {{-- ─── Campo (slot) ─────────────────────────────────────── --}}
    {{ $slot }}

    {{-- ─── Texto de Ayuda ──────────────────────────────────── --}}
    @if($ayuda)
    <p class="text-xs text-slate-500 leading-snug">{{ $ayuda }}</p>
    @endif

    {{-- ─── Error de Validación (Alpine reactivo) ─────────────── --}}
    <template x-if="errores['{{ $key }}']">
        <p class="text-xs text-red-400 font-medium"
            x-text="Array.isArray(errores['{{ $key }}']) ? errores['{{ $key }}'][0] : errores['{{ $key }}']"></p>
    </template>

</div>