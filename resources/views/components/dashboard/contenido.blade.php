<div x-data="{ currentRoute: window.location.hash || '#/' }" @hashchange.window="currentRoute = window.location.hash" class="h-full w-full">

    {{-- VISTA: Logins (Credenciales) --}}
    <template x-if="currentRoute === '#/logins'">
        <div class="h-full w-full">
            <x-logins.index />
        </div>
    </template>

    {{-- VISTA: Tarjetas --}}
    <template x-if="currentRoute === '#/tarjetas'">
        <div class="h-full w-full">
            <x-tarjetas.index />
        </div>
    </template>

    {{-- VISTAS EN DESARROLLO (Placeholder para otras opciones) --}}
    <template x-if="currentRoute !== '#/logins' && currentRoute !== '#/tarjetas'">
        <div class="h-full flex flex-col items-center justify-center text-center p-6">
            <div class="bg-white p-10 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200 max-w-lg w-full relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-blue-400 opacity-80 group-hover:opacity-100 transition-opacity"></div>

                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-6 text-2xl shadow-inner border border-blue-100">
                    🛡️
                </div>
                <h3 id="view-title" class="text-2xl font-semibold text-slate-900 mb-3 tracking-tight">Bienvenido a BITMAN Vault</h3>
                <p class="text-slate-600 mb-8 leading-relaxed">Selecciona una opción del menú lateral para comenzar a gestionar tus secretos y credenciales de manera segura.</p>

                <div class="inline-flex items-center px-3 py-1.5 rounded-md bg-slate-50 border border-slate-200">
                    <p class="text-xs text-slate-500 font-mono" id="view-path" x-text="`Ruta actual: ${currentRoute}`">Ruta actual: /</p>
                </div>
            </div>
        </div>
    </template>

</div>