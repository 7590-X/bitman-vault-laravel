<!-- ESTADO 4: ERROR / NOTA YA APERTURADA O EXPIRADA -->
<div x-show="paso === 'error'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    class="bg-slate-900/80 border border-slate-800 rounded-2xl p-8 shadow-2xl backdrop-blur-xl text-center relative overflow-hidden">
    <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center mx-auto mb-4 shadow-inner">
        <x-icon.alert-triangle class="w-8 h-8" />
    </div>
    <h2 class="text-xl font-bold text-white tracking-tight">No se pudo acceder a la Nota</h2>
    <p class="text-xs text-amber-300 mt-2 max-w-sm mx-auto leading-relaxed" x-text="mensajeError || 'Esta nota no existe, ya fue aperturada previamente o ha expirado.'"></p>

    <div class="mt-6 pt-4 border-t border-slate-800">
        <x-share.button
            variante="secondary"
            tipo="button"
            @click="paso = 'ingreso'; mensajeError = ''"
            class="px-4 py-2 text-xs font-semibold rounded-xl mx-auto">
            Intentar con otro código
        </x-share.button>
    </div>
</div>
