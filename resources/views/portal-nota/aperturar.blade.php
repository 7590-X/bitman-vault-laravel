<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lectura de Nota Segura - BITMAN Vault</title>

    <!-- Fuentes Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-slate-950 font-sans antialiased text-slate-100 selection:bg-blue-500 selection:text-white flex flex-col justify-between">

    <!-- Fondo con Efecto de Resplandor Neón -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Barra de Encabezado Superior Aislada -->
    <x-portal-nota.header />

    <!-- Contenido Principal -->
    <main class="relative z-10 flex-1 flex items-center justify-center p-4 sm:p-6 md:p-8">
        <div x-data="portalLecturaNotaComponent()" class="w-full max-w-xl">

            <!-- ESTADO 1: INGRESO DE CÓDIGO DE VERIFICACIÓN -->
            <x-portal-nota.ingreso />

            <!-- ESTADO 2: LECTURA DE NOTA Y TEMPORIZADOR DE AUTODESTRUCCIÓN -->
            <x-portal-nota.lectura />

            <!-- ESTADO 3: NOTA AUTODESTRUIDA -->
            <x-portal-nota.destruida />

            <!-- ESTADO 4: ERROR / NOTA YA APERTURADA O EXPIRADA -->
            <x-portal-nota.error />

        </div>
    </main>

    <!-- Pie de página -->
    <x-portal-nota.footer />

    <!-- Lógica Alpine.js del Portal -->
    <script>
        function portalLecturaNotaComponent() {
            return {
                tokenAcceso: '{{ $token }}',
                codigoApertura: '',
                paso: 'ingreso', // 'ingreso', 'cargando', 'lectura', 'destruida', 'error'
                cargando: false,
                notaDatos: null,
                tiempoRestante: 30,
                tiempoTotal: 30,
                porcentajeProgreso: 100,
                intervaloTimer: null,
                mostrarContenido: true,
                mensajeError: '',

                async aperturarNota() {
                    if (!this.codigoApertura || !this.codigoApertura.trim()) {
                        window.toastr.warning('Por favor ingresa el código de apertura', 'Código Requerido');
                        return;
                    }

                    this.cargando = true;

                    try {
                        const response = await fetch(`/api/enviar-notas/aperturar/${this.tokenAcceso}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                codigo_apertura: this.codigoApertura.trim()
                            })
                        });

                        const json = await response.json();

                        if (response.ok) {
                            this.notaDatos = json.payload || json.datos;
                            if (!this.notaDatos) {
                                this.mensajeError = 'No se recibieron los datos de la nota.';
                                this.paso = 'error';
                                return;
                            }
                            this.tiempoTotal = this.notaDatos.duracion_visualizacion_segundos || 30;
                            this.tiempoRestante = this.tiempoTotal;
                            this.porcentajeProgreso = 100;
                            this.paso = 'lectura';
                            this.iniciarTimer();
                            window.toastr.success('Nota aperturada correctamente', 'Éxito');
                        } else {
                            this.mensajeError = json.mensaje || 'No se pudo aperturar la nota';
                            this.paso = 'error';
                        }
                    } catch (e) {
                        this.mensajeError = 'Error procesando la respuesta del servidor.';
                        this.paso = 'error';
                    } finally {
                        this.cargando = false;
                    }
                },

                iniciarTimer() {
                    if (this.intervaloTimer) {
                        clearInterval(this.intervaloTimer);
                    }

                    this.intervaloTimer = setInterval(() => {
                        this.tiempoRestante--;
                        this.porcentajeProgreso = Math.max(0, (this.tiempoRestante / this.tiempoTotal) * 100);

                        if (this.tiempoRestante <= 0) {
                            this.destruirNota();
                        }
                    }, 1000);
                },

                destruirNota() {
                    if (this.intervaloTimer) {
                        clearInterval(this.intervaloTimer);
                    }

                    // Borrado inmediato del texto de la memoria JS
                    if (this.notaDatos) {
                        this.notaDatos.contenido = '';
                        this.notaDatos = null;
                    }

                    this.paso = 'destruida';
                    window.toastr.info('La nota ha sido autodestruida', 'Terminado');
                },

                async copiarContenido() {
                    if (!this.notaDatos || !this.notaDatos.contenido) return;

                    try {
                        await navigator.clipboard.writeText(this.notaDatos.contenido);
                        window.toastr.success('Texto copiado al portapapeles', 'Copiado');
                    } catch (e) {
                        window.toastr.error('No se pudo copiar el texto', 'Error');
                    }
                }
            };
        }
    </script>
</body>

</html>