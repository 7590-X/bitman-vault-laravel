{{--
    Componente formulario para crear un nuevo Envío de Nota Segura.
--}}
<div x-data="EnviarNotasFormularioCrearComponent" class="h-full max-w-xl flex flex-col min-h-0 overflow-hidden bg-slate-950">

    {{-- Encabezado del Formulario --}}
    <x-share.panel-header>
        <x-slot:icono>
            <x-icon.send-note class="w-5 h-5 text-blue-400" />
        </x-slot:icono>
        <x-slot:titulo>
            <span>Enviar Nueva Nota Segura</span>
        </x-slot:titulo>
        <x-slot:subtitulo>
            <span>Nota cifrada autoselectiva de un solo uso</span>
        </x-slot:subtitulo>
    </x-share.panel-header>

    {{-- Formulario --}}
    <form id="form-crear-nota-segura" @submit.prevent="guardar()" class="flex-1 flex flex-col min-h-0">
        <div class="max-w-xl flex-1 overflow-y-auto min-h-0 px-8 py-6 space-y-4">

            {{-- Campo: Título --}}
            <x-share.form-field etiqueta="Título de la Nota" nombre="titulo" :errores="$errors->get('titulo')">
                <x-share.input
                    nombre="titulo"
                    placeholder="Ej: Accesos temporales a la base de datos"
                    x-model="form.titulo"
                    required />
            </x-share.form-field>

            {{-- Campo: Correo Destino --}}
            <x-share.form-field etiqueta="Correo Electrónico del Destinatario" nombre="correo_destino" :errores="$errors->get('correo_destino')">
                <x-share.input
                    tipo="email"
                    nombre="correo_destino"
                    placeholder="destinatario@ejemplo.com"
                    x-model="form.correo_destino"
                    required />
            </x-share.form-field>

            {{-- Campo: Texto de la Nota --}}
            <x-share.form-field etiqueta="Contenido Secreto de la Nota" nombre="texto_nota" :errores="$errors->get('texto_nota')">
                <x-share.textarea
                    nombre="texto_nota"
                    placeholder="Escribe aquí la información confidencial que deseas compartir..."
                    rows="4"
                    x-model="form.texto_nota"
                    required />
            </x-share.form-field>

            {{-- Campo: Código de Apertura Personalizado (Opcional) --}}
            <x-share.form-field etiqueta="Código de Apertura (Opcional)" nombre="codigo_apertura">
                <x-share.input
                    nombre="codigo_apertura"
                    placeholder="Dejar vacío para autogenerar un código de 6 caracteres"
                    x-model="form.codigo_apertura" />
            </x-share.form-field>

            <div class="grid grid-cols-2 gap-4">
                {{-- Campo: Expiración en minutos --}}
                <x-share.form-field etiqueta="Validez del Enlace (Minutos)" nombre="minutos_expiracion">
                    <x-share.input
                        tipo="number"
                        min="1"
                        max="1440"
                        nombre="minutos_expiracion"
                        x-model.number="form.minutos_expiracion" />
                </x-share.form-field>

                {{-- Campo: Tiempo de lectura en segundos --}}
                <x-share.form-field etiqueta="Tiempo de Lectura (Segundos)" nombre="duracion_visualizacion_segundos">
                    <x-share.input
                        tipo="number"
                        min="5"
                        max="1000"
                        nombre="duracion_visualizacion_segundos"
                        x-model.number="form.duracion_visualizacion_segundos" />
                </x-share.form-field>
            </div>

            <div class="p-3 rounded-lg bg-blue-500/10 border border-blue-500/30 text-blue-300 text-xs">
                <p><strong>Seguridad CIA:</strong> Al enviar la nota, el texto será cifrado. Una vez que el destinatario ingrese el código y la lea, el contenido se borrará permanentemente de la base de datos.</p>
            </div>

        </div>

        {{-- Pie fijo con botones --}}
        <div class="shrink-0 py-4 px-8 border-t border-slate-700/80 bg-slate-950 flex items-center justify-end gap-3">
            <x-share.button
                variante="secondary"
                tipo="button"
                @click="cancelar()">
                Cancelar
            </x-share.button>

            <x-share.button
                variante="primary"
                tipo="submit"
                cargando="enviando">
                Crear y Enviar Nota
            </x-share.button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('EnviarNotasFormularioCrearComponent', () => ({
            enviando: false,
            errores: {},
            form: {
                titulo: '',
                correo_destino: '',
                texto_nota: '',
                codigo_apertura: '',
                minutos_expiracion: 30,
                duracion_visualizacion_segundos: 30
            },

            resetForm() {
                this.form = {
                    titulo: '',
                    correo_destino: '',
                    texto_nota: '',
                    codigo_apertura: '',
                    minutos_expiracion: 30,
                    duracion_visualizacion_segundos: 30
                };
                this.errores = {};
            },

            async guardar() {
                if (!this.form.titulo || !this.form.correo_destino || !this.form.texto_nota) {
                    window.toastr.warning('Completa los campos obligatorios del formulario', 'Incompleto');
                    return;
                }

                this.enviando = true;
                const token = localStorage.getItem('jwt_token');

                try {
                    const response = await fetch('/api/enviar-notas', {
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
                        window.toastr.success(json.mensaje || 'Nota registrada y enviada correctamente', 'Éxito');
                        this.resetForm();
                        this.$dispatch('nota-enviada-creada', json.datos);
                    } else {
                        window.toastr.error(json.mensaje || 'No se pudo registrar la nota', 'Error');
                    }
                } catch (e) {
                    window.toastr.error(e.message || 'Error de conexión', 'Error');
                } finally {
                    this.enviando = false;
                }
            }
        }))
    })
</script>
@endpush