{{--
    Componente reutilizable para visualizar una tarjeta.
    Puede funcionar de manera estática (pasando props Blade) o de forma reactiva (pasando el prefijo del objeto Alpine).
--}}
@props([
'alpineObject' => null,
'alias' => 'Nombre de Tarjeta',
'banco' => 'Banco Emisor',
'digitos' => '••••',
'titular' => 'TITULAR DE LA TARJETA',
'expiracion' => 'MM/YY',
])

<div {{ $attributes->merge(['class' => 'relative group cursor-pointer w-full max-w-sm mx-auto']) }}>
    {{-- Efecto de brillo detrás --}}
    <div class="absolute -inset-0.5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-[1.4rem] opacity-0 group-hover:opacity-100 blur transition-all duration-300 group-hover:duration-200"></div>

    <div class="relative flex flex-col justify-between h-56 bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/60 rounded-2xl p-6 shadow-xl overflow-hidden">
        {{-- Elementos decorativos de fondo --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>

        {{-- Header Tarjeta --}}
        <div class="flex justify-between items-start z-10">
            <div>
                <h4 class="text-lg font-semibold text-white tracking-wide truncate pr-4"
                    {!! $alpineObject ? "x-text=\" {$alpineObject}.alias\"" : '' !!}>
                    {{ $alias }}
                </h4>
                <p class="text-xs text-slate-400 uppercase tracking-wider mt-1"
                    {!! $alpineObject ? "x-text=\" {$alpineObject}.banco_emisor || 'Banco' \"" : '' !!}>
                    {{ $banco }}
                </p>
            </div>
            <div class="shrink-0 text-slate-300">
                {{-- Simular ícono de chip y contactless --}}
                <x-icon.chip class="w-8 h-8 opacity-80" />
            </div>
        </div>

        {{-- Número de Tarjeta --}}
        <div class="z-10 mt-6">
            <div class="flex items-center gap-3 text-slate-300 font-mono text-lg sm:text-xl tracking-widest">
                <span>••••</span>
                <span>••••</span>
                <span>••••</span>
                <span class="text-white font-semibold"
                    {!! $alpineObject ? "x-text=\" {$alpineObject}.ultimos_4_digitos\"" : '' !!}>
                    {{ $digitos }}
                </span>
            </div>
        </div>

        {{-- Footer Tarjeta --}}
        <div class="flex justify-between items-end z-10 mt-4">
            <div class="flex flex-col">
                <span class="text-[10px] text-slate-400 uppercase tracking-widest">Titular</span>
                <span class="text-sm text-slate-200 font-medium tracking-wide truncate max-w-[150px]"
                    {!! $alpineObject ? "x-text=\" {$alpineObject}.nombre_titular\"" : '' !!}>
                    {{ $titular }}
                </span>
            </div>
            <div class="flex flex-col items-end">
                <span class="text-[10px] text-slate-400 uppercase tracking-widest">Expira</span>
                <span class="text-sm text-slate-200 font-medium tracking-wide"
                    {!! $alpineObject ? "x-text=\" formatearFecha({$alpineObject}.fecha_expiracion)\"" : '' !!}>
                    {{ $expiracion }}
                </span>
            </div>
        </div>
    </div>
</div>