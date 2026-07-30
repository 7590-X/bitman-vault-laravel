{{--
    Componente independiente de formulario para editar un Login existente.
    Se despliega dentro del panel derecho del dashboard (vista de pantalla completa del panel).
    Utiliza los componentes compartidos estandarizados en <x-share.*>.
--}}
<div
    x-data="{
        enviando: false,
        mostrarPassword: false,
        errores: {},
        errorGeneral: null,
        form: {
            nombre_sitio: '',
            url: '',
            usuario_login: '',
            contrasena_encriptada: '',
            notas: ''
        },

        init() {
            this.resetForm();
        },

        resetForm() {
            if (this.loginSeleccionado) {
                this.form = {
                    nombre_sitio: this.loginSeleccionado.nombre_sitio || '',
                    url: this.loginSeleccionado.url || '',
                    usuario_login: this.loginSeleccionado.usuario_login || '',
                    contrasena_encriptada: this.loginSeleccionado.contrasena_encriptada || '',
                    notas: this.loginSeleccionado.notas || ''
                };
            }
            this.errores = {};
            this.errorGeneral = null;
            this.mostrarPassword = false;
        },

        validarLocal() {
            this.errores = {};
            if (!this.form.nombre_sitio || !this.form.nombre_sitio.trim()) {
                this.errores.nombre_sitio = ['El nombre del sitio es obligatorio.'];
            }
            if (!this.form.contrasena_encriptada) {
                this.errores.contrasena_encriptada = ['La contraseña es obligatoria.'];
            }
            return Object.keys(this.errores).length === 0;
        },

        async actualizar() {
            if (!this.validarLocal()) return;
            this.enviando = true;
            this.errorGeneral = null;
            const token = localStorage.getItem('jwt_token');
            try {
                const response = await fetch(`/api/logins/${this.loginSeleccionado.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });
                const json = await response.json();

                if (response.ok || response.status === 200) {
                    
                    $dispatch('login-actualizado', json.datos);
                } else if (response.status === 422) {
                    this.errores = json.errors || {};
                    this.errorGeneral = json.message || 'Corrige los errores del formulario.';
                } else {
                    this.errorGeneral = json.mensaje || 'Ocurrió un error al intentar actualizar.';
                    window.toastr.error(this.errorGeneral, 'Error');
                }
            } catch (e) {
                this.errorGeneral = 'Error de conexión con el servidor.';
                window.toastr.error(this.errorGeneral, 'Error');
            } finally {
                this.enviando = false;
            }
        }
    }"
    class="h-full flex flex-col max-w-xl min-h-0">
    {{-- ─── Encabezado del Formulario ──────────────────────────────────────── --}}
    <x-share.panel-header
        titulo="Editar Login"
        subtitulo="Modifica los datos de este secreto.">
        <x-slot:icono>
            <x-icon.globe class="w-5 h-5" />
        </x-slot:icono>
    </x-share.panel-header>

    {{-- ─── Cuerpo del Formulario (scrolleable) ────────────────────────────── --}}
    <div class="px-8 py-4 flex-1 overflow-y-auto min-h-0">
        <form @submit.prevent="actualizar()" id="form-editar-login" class=" space-y-5">

            {{-- Alerta de Error General --}}
            <template x-if="errorGeneral">
                <x-share.alert tipo="error">
                    <span x-text="errorGeneral"></span>
                </x-share.alert>
            </template>

            {{-- Fila 1: Nombre del sitio (ancho completo) --}}
            <x-share.input
                nombre="nombre_sitio"
                etiqueta="Nombre del sitio"
                placeholder="Ej. GitHub, Google, access.broadcom.com"
                :requerido="true"
                x-model="form.nombre_sitio" />

            {{-- Fila 2: URL del sitio (ancho completo) --}}
            <x-share.input
                nombre="url"
                etiqueta="URL del sitio"
                tipo="url"
                placeholder="https://example.com"
                x-model="form.url" />

            {{-- Fila 3: Usuario / Correo (ancho completo) --}}
            <x-share.input
                nombre="usuario_login"
                etiqueta="Usuario o Correo"
                placeholder="usuario@correo.com"
                x-model="form.usuario_login" />

            {{-- Fila 4: Contraseña (ancho completo) --}}
            <x-share.password
                nombre="contrasena_encriptada"
                etiqueta="Contraseña"
                placeholder="••••••••••••"
                :requerido="true"
                x-model="form.contrasena_encriptada" />

            {{-- Fila 5: Notas adicionales --}}
            <x-share.textarea
                nombre="notas"
                etiqueta="Notas adicionales"
                placeholder="Notas adicionales sobre este acceso..."
                :filas="3"
                x-model="form.notas" />

        </form>
    </div>

    {{-- ─── Pie fijo con acciones ──────────────────────────────────────────── --}}
    <div class="shrink-0 px-8 py-4 border-t border-slate-700/80 bg-slate-950 flex items-center justify-end gap-3">
        <x-share.button
            variante="secondary"
            tipo="button"
            @click="cancelarEdicion()"
            cargando="enviando">
            Cancelar
        </x-share.button>
        <x-share.button
            variante="primary"
            tipo="submit"
            form="form-editar-login"
            cargando="enviando">
            Actualizar Login
        </x-share.button>
    </div>
</div>