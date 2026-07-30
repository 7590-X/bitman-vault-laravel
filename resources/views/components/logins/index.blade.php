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
        editando: false,
        mensajeExito: null,

        async init() {
            await this.cargarLogins();
        },

        iniciarCreacion() {
            this.creando = true;
            this.editando = false;
        },

        cancelarCreacion() {
            this.creando = false;
        },

        iniciarEdicion() {
            this.editando = true;
            this.creando = false;
        },

        cancelarEdicion() {
            this.editando = false;
        },

        seleccionarLogin(login) {
            if (this.creando || this.editando) {
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
        },

        async eliminarLogin() {
            if (!confirm('¿Estás seguro de que deseas eliminar este login? Esta acción no se puede deshacer.')) return;
            
            const token = localStorage.getItem('jwt_token');
            try {
                const response = await fetch(`/api/logins/${this.loginSeleccionado.id_login}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                if (response.ok || response.status === 200 || response.status === 204) {
                    this.loginSeleccionado = null;
                    this.mensajeExito = '¡Login eliminado exitosamente!';
                    await this.cargarLogins();
                } else {
                    //alert('No se pudo eliminar el login.');
                }
            } catch(e) {
                //alert('Error de conexión al intentar eliminar.');
            }
        }
    }"
    @login-creado.window="
        await cargarLogins();
        if($event.detail)
            loginSeleccionado = $event.detail;
            creando = false;
        "

    @login-actualizado.window="
        await cargarLogins();
        if($event.detail)
            loginSeleccionado = $event.detail;
            editando = false;
        "

    class="h-full w-full flex border border-slate-800 bg-slate-900 overflow-hidden shadow-xl">

    {{-- Panel Izquierdo: Lista de Logins (Componente Independiente) --}}
    <div class="w-80 md:w-96 shrink-0 h-full">
        <x-logins.lista />
    </div>

    {{-- Panel Derecho: Formulario de Creación O Detalle del Login Seleccionado --}}
    <div class="flex-1 h-full flex flex-col overflow-hidden p-6"
        :class="(creando || editando) ? 'bg-slate-950' : 'bg-slate-950/60'">

        {{-- Modo Creación: Despliega el formulario embebido en el panel derecho --}}
        <div x-show="creando" class="h-full">
            <x-logins.formulario-crear />
        </div>

        {{-- Modo Edición: Despliega el formulario de edición en el panel derecho --}}
        <div x-show="editando" class="h-full">
            <template x-if="editando && loginSeleccionado">
                <x-logins.formulario-editar />
            </template>
        </div>

        {{-- Modo Detalle: Despliega los datos del login seleccionado --}}
        <template x-if="!creando && !editando && loginSeleccionado">
            <x-logins.detalle />
        </template>

        {{-- Modo Vacío: Cuando no hay selección activa --}}
        <template x-if="!creando && !editando && !loginSeleccionado && !cargando">
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