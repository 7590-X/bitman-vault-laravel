{{--
    Componente maestro para la gestión de Logins en el Dashboard.
--}}
<div
    x-data="{
        logins: [],
        cargando: true,
        error: null,
        loginSeleccionado: null,
        creando: false,
        mensajeExito: null,

        async init() {
            await this.cargarLogins();
        },

        iniciarCreacion() {
            this.creando = true;
        },

        cancelarCreacion() {
            this.creando = false;
        },

        seleccionarLogin(login) {
            if (this.creando) {
                return;
            }
            this.loginSeleccionado = login;
        },

        async cargarLogins() {
            this.cargando = true;
            this.error = null;
            const token = localStorage.getItem('jwt_token');

            try {
                const response = await fetch('/api/logins', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const json = await response.json();
                    this.logins = json.datos || [];
                    if (this.logins.length > 0 && !this.loginSeleccionado) {
                        this.loginSeleccionado = this.logins[0];
                    }
                } else {
                    this.logins = [];
                }
            } catch (e) {
                this.error = 'Error de conexión';
                this.logins = [];
            } finally {
                this.cargando = false;
            }
        }
    }"
    @login-creado.window="await cargarLogins(); if($event.detail) loginSeleccionado = $event.detail; creando = false; mensajeExito = '¡Login registrado exitosamente en tu bóveda!'"
    class="h-full w-full flex border border-slate-800 bg-slate-900 overflow-hidden shadow-xl">

    {{-- Diálogo / Modal de Éxito --}}
    <div
        x-show="mensajeExito"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 max-w-sm w-full text-center space-y-4 shadow-2xl">
            <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white tracking-tight">¡Registro Exitoso!</h3>
            <p class="text-xs text-slate-300 leading-relaxed" x-text="mensajeExito"></p>
            <button
                type="button"
                @click="mensajeExito = null"
                class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Aceptar
            </button>
        </div>
    </div>

    {{-- Panel Izquierdo: Lista de Logins (Componente Independiente) --}}
    <div class="w-80 md:w-96 shrink-0 h-full">
        <x-logins.lista />
    </div>

    {{-- Panel Derecho: Formulario de Creación O Detalle del Login Seleccionado --}}
    <div class="flex-1 h-full flex flex-col overflow-hidden p-6"
        :class="creando ? 'bg-slate-950' : 'bg-slate-950/60'">

        {{-- Modo Creación: Despliega el formulario embebido en el panel derecho --}}
        <div x-show="creando" class="h-full">
            <x-logins.formulario-crear />
        </div>

        {{-- Modo Detalle: Despliega los datos del login seleccionado --}}
        <template x-if="!creando && loginSeleccionado">
            <x-logins.detalle />
        </template>

        {{-- Modo Vacío: Cuando no hay selección activa --}}
        <template x-if="!creando && !loginSeleccionado && !cargando">
            <div class="h-full flex flex-col items-center justify-center text-center p-8">
                <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 mb-4 shadow-inner">
                    <x-icon.vault class="w-8 h-8 opacity-40" />
                </div>
                <h3 class="text-base font-medium text-slate-300">Selecciona un elemento</h3>
                <p class="text-xs text-slate-500 max-w-xs mt-1">Haz clic sobre un login de la lista lateral para ver los detalles de la credencial.</p>
            </div>
        </template>

    </div>

</div>