@props([
    'subtitulo' => 'Bóveda de Lectura Segura Unilateral',
    'badgeText' => 'Portal Público de Acceso',
])

<header {{ $attributes->merge(['class' => 'relative z-10 w-full border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md px-6 py-4']) }}>
    <div class="max-w-4xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600/20 border border-blue-500/40 flex items-center justify-center text-blue-400 shadow-inner">
                <x-icon.lock class="w-5 h-5" />
            </div>
            <div>
                <h1 class="text-base font-bold tracking-tight text-white flex items-center gap-2">
                    BITMAN <span class="text-blue-500 font-extrabold">VAULT</span>
                </h1>
                <p class="text-[11px] text-slate-400 font-mono uppercase tracking-wider">{{ $subtitulo }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/60 border border-slate-700/60 text-xs text-slate-300">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>{{ $badgeText }}</span>
        </div>
    </div>
</header>
