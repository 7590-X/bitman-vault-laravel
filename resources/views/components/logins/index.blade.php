{{--
    Componente maestro para la gestión de Logins en el Dashboard.
--}}
<div
    x-data="{
        logins: [],
        cargando: true,
        error: null,
        loginSeleccionado: null,

        async init() {
            await this.cargarLogins();
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
    class="h-full w-full flex border border-slate-800 bg-slate-900 overflow-hidden shadow-xl">

    {{-- Panel Izquierdo: Lista de Logins (Componente Independiente) --}}
    <div class="w-80 md:w-96 shrink-0 h-full">
        <x-logins.lista />
    </div>

    {{-- Panel Derecho: Detalle del Login Seleccionado --}}
    <div class="flex-1 h-full flex flex-col bg-slate-950/60 p-6 md:p-8 overflow-y-auto">

        <template x-if="loginSeleccionado">
            <div class="max-w-2xl w-full mx-auto space-y-6">
                {{-- Cabecera del detalle --}}
                <div class="flex items-start justify-between pb-5 border-b border-slate-800">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-slate-800 border border-slate-700/80 flex items-center justify-center text-blue-400 shadow-md">
                            <x-icon.globe class="w-8 h-8" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white tracking-tight" x-text="loginSeleccionado.nombre_sitio"></h2>
                            <p class="text-sm text-slate-400 font-mono mt-0.5" x-text="loginSeleccionado.url || 'Sin URL registrada'"></p>
                        </div>
                    </div>
                </div>

                {{-- Campos del detalle --}}
                <div class="grid grid-cols-1 gap-4">
                    {{-- Campo: Usuario --}}
                    <div class="bg-slate-900/80 p-4 rounded-lg border border-slate-800">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Usuario / Correo</label>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-100 font-mono" x-text="loginSeleccionado.usuario_login || '—'"></span>
                        </div>
                    </div>

                    {{-- Campo: Contraseña Encriptada --}}
                    <div class="bg-slate-900/80 p-4 rounded-lg border border-slate-800">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Contraseña (Encriptada)</label>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-mono text-slate-300 truncate max-w-md" x-text="loginSeleccionado.contrasena_encriptada || '••••••••'"></span>
                        </div>
                    </div>

                    {{-- Campo: Notas adicionales --}}
                    <template x-if="loginSeleccionado.notas">
                        <div class="bg-slate-900/80 p-4 rounded-lg border border-slate-800">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Notas</label>
                            <p class="text-sm text-slate-300 leading-relaxed whitespace-pre-line" x-text="loginSeleccionado.notas"></p>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <template x-if="!loginSeleccionado && !cargando">
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