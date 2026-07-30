{{--
    Componente reutilizable para mostrar un campo de dato en vistas de detalle.

    Props:
      - etiqueta : string — Label del campo (requerido)
      - oculto   : bool   — Oculta el valor con ••• y agrega toggle Ver/Ocultar (default: false)
      - copiable : bool   — Muestra botón Copiar al portapapeles (default: false)

    Slot (default):
      El contenido del valor. Usar <span x-text="..."> para valores dinámicos Alpine.
      El nodo siempre está en el DOM (oculto con CSS) para que el Clipboard API pueda leer el texto.
--}}
@props([
    'etiqueta' => '',
    'oculto'   => false,
    'copiable' => false,
])

<div
    x-data="{
        visible: false,
        copiado: false,
        async copiar() {
            const el = $refs.valorTexto;
            const texto = el ? el.textContent.trim() : '';
            if (!texto || texto === '—') return;
            try {
                await navigator.clipboard.writeText(texto);
                this.copiado = true;
                setTimeout(() => this.copiado = false, 2000);
            } catch(e) {}
        }
    }"
    class="rounded-lg border border-slate-700/80 bg-slate-900 overflow-hidden"
>
    {{-- ─── Header: Etiqueta + Acciones ────────────────────────── --}}
    <div class="px-4 py-2 border-b border-slate-700/60 bg-slate-800/40 flex items-center justify-between gap-2">
        <span class="text-xs font-semibold uppercase tracking-widest text-slate-400 leading-none">
            {{ $etiqueta }}
        </span>

        <div class="flex items-center gap-1.5 shrink-0">

            @if($oculto)
            {{-- Botón: Ver / Ocultar --}}
            <button
                type="button"
                @click="visible = !visible"
                class="inline-flex items-center gap-1 text-xs font-medium text-slate-400 hover:text-slate-100 bg-slate-800 hover:bg-slate-700 border border-slate-700/60 rounded px-2 py-0.5 transition-colors select-none"
            >
                <x-icon.eye-off x-show="visible" class="w-3.5 h-3.5 shrink-0" />
                <x-icon.eye    x-show="!visible" class="w-3.5 h-3.5 shrink-0" />
                <span x-text="visible ? 'Ocultar' : 'Ver'"></span>
            </button>
            @endif

            @if($copiable)
            {{-- Botón: Copiar --}}
            <button
                type="button"
                @click="copiar()"
                class="inline-flex items-center gap-1 text-xs font-medium border rounded px-2 py-0.5 transition-all select-none"
                :class="copiado
                    ? 'text-emerald-400 border-emerald-500/40 bg-emerald-500/10'
                    : 'text-slate-400 hover:text-slate-100 bg-slate-800 hover:bg-slate-700 border-slate-700/60'"
            >
                <x-icon.copy  x-show="!copiado" class="w-3.5 h-3.5 shrink-0" />
                <x-icon.check x-show="copiado"  class="w-3.5 h-3.5 shrink-0" />
                <span x-text="copiado ? 'Copiado' : 'Copiar'"></span>
            </button>
            @endif

        </div>
    </div>

    {{-- ─── Valor del campo ─────────────────────────────────────── --}}
    <div class="px-4 py-3 min-h-[2.75rem] flex items-center">

        @if($oculto)
            {{--
                Siempre renderizamos el slot en el DOM (oculto visualmente con CSS)
                para que $refs.valorTexto tenga el texto disponible para el Clipboard API.
            --}}
            {{-- Asteriscos: visibles cuando el campo está oculto --}}
            <span
                x-show="!visible"
                class="text-sm font-mono text-slate-500 tracking-[0.3em] select-none"
                aria-hidden="true"
            >••••••••••••</span>
            {{-- Valor real: siempre en el DOM, visible o invisible según toggle --}}
            <span
                x-ref="valorTexto"
                :class="visible ? 'text-slate-100' : 'sr-only'"
                class="text-sm font-mono break-all"
            >{{ $slot }}</span>
        @else
            {{-- Campo normal / sólo copiable --}}
            <span
                x-ref="valorTexto"
                class="text-sm font-mono text-slate-100 break-all"
            >{{ $slot }}</span>
        @endif

    </div>
</div>
