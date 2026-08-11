<!-- ESTADO 1: INGRESO DE CÓDIGO DE VERIFICACIÓN -->
<div x-show="paso === 'ingreso'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-600"></div>

    <div class="text-center mb-6">
        <div class="w-16 h-16 rounded-2xl bg-blue-500/10 border border-blue-500/30 text-blue-400 flex items-center justify-center mx-auto mb-4 shadow-inner">
            <x-icon.key class="w-8 h-8" />
        </div>
        <h2 class="text-xl font-bold text-white tracking-tight">Ingresa el Código de Apertura</h2>
        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Esta nota está encriptada y protegida. Introduce el código de verificación enviado por el emisor para aperturarla.</p>
    </div>

    <form @submit.prevent="aperturarNota()" class="space-y-5">
        <div>
            <label for="codigo_apertura" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 text-center">
                Código de Verificación
            </label>
            <input
                type="text"
                id="codigo_apertura"
                x-model="codigoApertura"
                placeholder="Ej. AB12CD"
                required
                autocomplete="off"
                class="w-full text-center tracking-[0.4em] font-mono text-2xl font-bold py-3.5 px-4 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 uppercase transition-all shadow-inner">
        </div>

        <x-share.button
            tipo="submit"
            variante="primary"
            cargando="cargando"
            class="w-full">
            <template x-if="!cargando">
                <div class="flex items-center gap-2">
                    <x-icon.lock />
                    <span>Aperturar Nota Secreta</span>
                </div>
            </template>
            <template x-if="cargando">
                <span>Verificando código...</span>
            </template>
        </x-share.button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-800/80 text-center">
        <p class="text-[11px] text-slate-500">
            Esta nota es de lectura única y se autodestruirá inmediatamente después de visualizarla.
        </p>
    </div>
</div>
