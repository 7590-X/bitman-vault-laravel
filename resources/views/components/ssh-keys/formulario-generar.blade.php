{{--
    Componente independiente de formulario para Generar una nueva Llave SSH.
    Se despliega dentro del panel derecho del dashboard.
    Utiliza los componentes compartidos estandarizados en <x-share.*>.
--}}
<div x-data="SshKeysFormularioGenerarComponent" class="h-full flex flex-col max-w-xl min-h-0">

    {{-- ─── Encabezado del Formulario ──────────────────────────────────────── --}}
    <x-share.panel-header
        titulo="Generar Llave SSH"
        subtitulo="Configura y genera un nuevo par de llaves criptográficas localmente.">
        <x-slot:icono>
            <x-icon.key class="w-5 h-5" />
        </x-slot:icono>
    </x-share.panel-header>

    {{-- ─── Cuerpo del Formulario (scrolleable) ────────────────────────────── --}}
    <div class="flex-1 px-8 py-4 overflow-y-auto min-h-0">
        <form @submit.prevent="generar()" id="form-generar-ssh" class="space-y-5">

            {{-- Fila 1: Algoritmo --}}
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-300">Algoritmo</label>
                <select x-model="form.algorithm" @change="actualizarOpciones()" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/50">
                    <option value="RSA">RSA (Estándar de facto)</option>
                    <option value="ECDSA">ECDSA (Curva elíptica, moderno y eficiente)</option>
                </select>
            </div>

            {{-- Fila 2: Tamaño / Curva --}}
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-300">
                    <span x-show="form.algorithm === 'RSA'">Tamaño de llave (bits)</span>
                    <span x-show="form.algorithm === 'ECDSA'">Curva Nombrada</span>
                </label>
                <select x-model="form.parametroAlgoritmo" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/50">
                    <template x-if="form.algorithm === 'RSA'">
                        <option value="2048">2048 (Recomendado)</option>
                    </template>
                    <template x-if="form.algorithm === 'RSA'">
                        <option value="4096">4096 (Mayor seguridad, más lento)</option>
                    </template>

                    <template x-if="form.algorithm === 'ECDSA'">
                        <option value="P-256">P-256 (NIST P-256)</option>
                    </template>
                    <template x-if="form.algorithm === 'ECDSA'">
                        <option value="P-384">P-384 (NIST P-384)</option>
                    </template>
                    <template x-if="form.algorithm === 'ECDSA'">
                        <option value="P-521">P-521 (NIST P-521)</option>
                    </template>
                </select>
            </div>

            {{-- Fila 3: Comentario --}}
            <x-share.input
                nombre="comment"
                etiqueta="Comentario"
                placeholder="Ej. user@servidor"
                :requerido="false"
                x-model="form.comment" />

            {{-- Fila 4: Frase de Paso (Passphrase) --}}
            <x-share.password
                nombre="passphrase"
                etiqueta="Frase de paso (Passphrase)"
                placeholder="Opcional. Para mayor seguridad."
                :requerido="false"
                x-model="form.passphrase" />

        </form>
    </div>

    {{-- ─── Pie fijo con acciones ──────────────────────────────────────────── --}}
    <div class="shrink-0 py-4 px-8 border-t border-slate-700/80 bg-slate-950 flex items-center justify-end gap-3">
        <x-share.button
            variante="secondary"
            tipo="button"
            @click="cancelar()"
            cargando="generando">
            Cancelar
        </x-share.button>
        <x-share.button
            variante="primary"
            tipo="submit"
            form="form-generar-ssh"
            cargando="generando">
            Generar y Usar
        </x-share.button>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('SshKeysFormularioGenerarComponent', () => ({
            generando: false,
            form: {
                algorithm: 'RSA',
                parametroAlgoritmo: '2048', // Puede ser modulusLength o namedCurve
                comment: 'user@bitman-vault',
                passphrase: ''
            },

            actualizarOpciones() {
                if (this.form.algorithm === 'RSA') {
                    this.form.parametroAlgoritmo = '2048';
                } else {
                    this.form.parametroAlgoritmo = 'P-256';
                }
            },

            cancelar() {
                this.$dispatch('cerrar-generador-ssh');
            },

            async generar() {
                this.generando = true;
                
                let options = {
                    algorithm: this.form.algorithm,
                    comment: this.form.comment
                };

                if (this.form.algorithm === 'RSA') {
                    options.modulusLength = parseInt(this.form.parametroAlgoritmo);
                } else {
                    options.namedCurve = this.form.parametroAlgoritmo;
                }

                try {
                    // Llamar a la librería SSHKeyGenerator globalmente expuesta
                    const result = await window.SSHKeyGenerator.generate(options);
                    
                    // Notificar al componente padre que se generaron
                    this.$dispatch('ssh-key-generada', {
                        publicKeyOpenSSH: result.publicKeyOpenSSH,
                        privateKeyPem: result.privateKeyPem,
                        passphrase: this.form.passphrase
                    });
                    
                    window.toastr.success('Llaves SSH generadas exitosamente localmente.', 'Generado');
                    this.form.passphrase = ''; // Limpiar por seguridad
                } catch (e) {
                    window.toastr.error(e.message || 'Error al generar las llaves', 'Error');
                } finally {
                    this.generando = false;
                }
            }
        }));
    });
</script>
@endpush
