<!-- ESTADO 3: NOTA AUTODESTRUIDA -->
<div x-show="paso === 'destruida'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    class="bg-slate-900/80 border border-slate-800 rounded-2xl p-8 shadow-2xl backdrop-blur-xl text-center relative overflow-hidden">
    <div class="w-16 h-16 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center justify-center mx-auto mb-4 shadow-inner">
        <x-icon.trash class="w-8 h-8" />
    </div>
    <h2 class="text-xl font-bold text-white tracking-tight">Nota Autodestruida</h2>
    <p class="text-xs text-slate-400 mt-2 max-w-sm mx-auto leading-relaxed">
        El contenido confidencial de esta nota ha sido borrado permanentemente de la memoria del navegador y de la base de datos.
    </p>
    <div class="mt-6 p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-500 font-mono">
        Estado: Eliminado de forma permanente
    </div>
</div>
