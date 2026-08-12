{{--
    Componente independiente de formulario para registrar una nueva Llave SSH.
    Se despliega dentro del panel derecho del dashboard.
    Utiliza los componentes compartidos estandarizados en <x-share.*>.
--}}
<div x-data="SshKeysFormularioCrearComponent" 
     @ssh-key-generada.window="asignarLlavesGeneradas($event.detail)"
     class="h-full flex flex-col max-w-xl min-h-0">

    {{-- ─── Encabezado del Formulario ──────────────────────────────────────── --}}
    <x-share.panel-header
        titulo="Registrar Nueva Llave SSH"
        subtitulo="Ingresa los datos de tu llave SSH existente para almacenarla de forma segura.">
        <x-slot:icono>
            <x-icon.key class="w-5 h-5" />
        </x-slot:icono>
    </x-share.panel-header>

    {{-- ─── Cuerpo del Formulario (scrolleable) ────────────────────────────── --}}
    <div class="flex-1 px-8 py-4 overflow-y-auto min-h-0">
        <form @submit.prevent="guardar()" id="form-crear-ssh" class="space-y-5">

            {{-- Fila 1: Nombre de la Llave --}}
            <x-share.input
                nombre="nombre"
                etiqueta="Nombre de la llave"
                placeholder="Ej. Servidor Producción, AWS EC2, GitHub"
                :requerido="true"
                x-model="form.nombre" />

            {{-- Fila 2: Llave Pública --}}
            <x-share.textarea
                nombre="llave_publica"
                etiqueta="Llave Pública"
                placeholder="ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQC..."
                :requerido="true"
                :filas="4"
                x-model="form.llave_publica" />

            {{-- Fila 3: Llave Privada --}}
            <x-share.textarea
                nombre="llave_privada_encriptada"
                etiqueta="Llave Privada"
                placeholder="-----BEGIN OPENSSH PRIVATE KEY-----..."
                :requerido="true"
                :filas="6"
                x-model="form.llave_privada_encriptada" />

            {{-- Fila 4: Frase de Paso (Passphrase) --}}
            <x-share.password
                nombre="frase_paso_encriptada"
                etiqueta="Frase de paso (Passphrase)"
                placeholder="Opcional. Deja en blanco si no tiene."
                :requerido="false"
                x-model="form.frase_paso_encriptada" />

        </form>
    </div>

    {{-- ─── Pie fijo con acciones ──────────────────────────────────────────── --}}
    <div class="shrink-0 py-4 px-8 border-t border-slate-700/80 bg-slate-950 flex items-center justify-between gap-3">
        <x-share.button
            variante="secondary"
            tipo="button"
            @click="generarNueva()"
            cargando="false">
            Generar Nueva
        </x-share.button>

        <div class="flex items-center gap-3">
            <x-share.button
                variante="secondary"
                tipo="button"
                @click="cancelar()"
                cargando="enviando">
                Cancelar
            </x-share.button>
            <x-share.button
                variante="primary"
                tipo="submit"
                form="form-crear-ssh"
                cargando="enviando">
                Guardar SSH Key
            </x-share.button>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('SshKeysFormularioCrearComponent', () => ({
            enviando: false,
            mostrarPassword: false,
            errores: {},
            errorGeneral: null,
            form: {
                nombre: '',
                llave_publica: '',
                llave_privada_encriptada: '',
                frase_paso_encriptada: ''
            },

            resetForm() {
                this.form = {
                    nombre: '',
                    llave_publica: '',
                    llave_privada_encriptada: '',
                    frase_paso_encriptada: ''
                };
                this.errores = {};
                this.errorGeneral = null;
                this.mostrarPassword = false;
            },

            validarLocal() {
                this.errores = {};
                if (!this.form.nombre || !this.form.nombre.trim()) {
                    this.errores.nombre = ['El nombre de la llave es obligatorio.'];
                }
                if (!this.form.llave_publica || !this.form.llave_publica.trim()) {
                    this.errores.llave_publica = ['La llave pública es obligatoria.'];
                }
                if (!this.form.llave_privada_encriptada || !this.form.llave_privada_encriptada.trim()) {
                    this.errores.llave_privada_encriptada = ['La llave privada es obligatoria.'];
                }
                return Object.keys(this.errores).length === 0;
            },

            generarNueva() {
                this.$dispatch('abrir-generador-ssh');
            },

            asignarLlavesGeneradas(detalle) {
                this.form.llave_publica = detalle.publicKeyOpenSSH;
                this.form.llave_privada_encriptada = detalle.privateKeyPem;
                this.form.frase_paso_encriptada = detalle.passphrase || '';
            },

            async guardar() {
                if (!this.validarLocal()) {
                    window.toastr.warning('Valida los campos del formulario', 'Formulario Incompleto');
                    return;
                }

                this.enviando = true;
                this.errorGeneral = null;
                const token = localStorage.getItem('jwt_token');

                try {
                    const response = await fetch('/api/llaves-ssh', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.form)
                    });

                    const json = await response.json();

                    if (response.ok || response.status === 201) {
                        this.resetForm();
                        window.toastr.success('Llave SSH registrada correctamente', 'Nueva Llave')
                        this.$dispatch('llave-ssh-creada', json.datos);
                    } else if (response.status === 422) {
                        this.errores = json.errors || {};
                        this.errorGeneral = json.message || 'Corrige los errores del formulario.';
                    } else {
                        this.errorGeneral = json.mensaje || 'Ocurrió un error al intentar guardar.';
                    }
                } catch (e) {
                    this.errorGeneral = 'Error de conexión con el servidor.';
                } finally {
                    if (this.errorGeneral !== null) {
                        window.toastr.error(this.errorGeneral, 'Error al Guardar')
                    }
                    this.enviando = false;
                }
            }
        }));
    });
</script>
@endpush
