{{--
    Componente de indicador de fortaleza de contraseña.
    Muestra una barra visual y etiqueta del nivel en tiempo real via JS.
    Props:
      - campoObjetivo: string — id del input de contraseña a observar
--}}
@props(['campoObjetivo' => 'contrasena'])

<div id="indicador-fortaleza-{{ $campoObjetivo }}" class="flex flex-col gap-1.5 hidden">
    <div class="flex gap-1" role="progressbar" aria-label="Fortaleza de la contraseña">
        <div class="segmento-fortaleza h-1.5 flex-1 rounded-sm bg-slate-200 transition-colors duration-300" data-nivel="1"></div>
        <div class="segmento-fortaleza h-1.5 flex-1 rounded-sm bg-slate-200 transition-colors duration-300" data-nivel="2"></div>
        <div class="segmento-fortaleza h-1.5 flex-1 rounded-sm bg-slate-200 transition-colors duration-300" data-nivel="3"></div>
        <div class="segmento-fortaleza h-1.5 flex-1 rounded-sm bg-slate-200 transition-colors duration-300" data-nivel="4"></div>
    </div>
    <p class="etiqueta-fortaleza text-xs font-medium text-slate-500"></p>
</div>

<script>
(function () {
    /**
     * Calcula el nivel de fortaleza de la contraseña (0-4).
     */
    function calcularFortaleza(contrasena) {
        let puntos = 0;
        if (contrasena.length >= 12) puntos++;
        if (/[A-Z]/.test(contrasena)) puntos++;
        if (/[0-9]/.test(contrasena)) puntos++;
        if (/[\W_]/.test(contrasena)) puntos++;
        return puntos;
    }

    const colores = {
        1: 'bg-red-500',
        2: 'bg-amber-500',
        3: 'bg-blue-500',
        4: 'bg-emerald-500',
    };

    const etiquetas = {
        0: '',
        1: 'Muy débil',
        2: 'Débil',
        3: 'Aceptable',
        4: 'Segura',
    };

    const coloresEtiqueta = {
        1: 'text-red-700',
        2: 'text-amber-700',
        3: 'text-blue-700',
        4: 'text-emerald-700',
    };

    document.addEventListener('DOMContentLoaded', function () {
        const campo = document.getElementById('{{ $campoObjetivo }}');
        const contenedor = document.getElementById('indicador-fortaleza-{{ $campoObjetivo }}');

        if (!campo || !contenedor) return;

        const segmentos = contenedor.querySelectorAll('.segmento-fortaleza');
        const etiqueta  = contenedor.querySelector('.etiqueta-fortaleza');

        campo.addEventListener('input', function () {
            const valor = campo.value;

            if (valor.length === 0) {
                contenedor.classList.add('hidden');
                return;
            }

            contenedor.classList.remove('hidden');
            const nivel = calcularFortaleza(valor);

            segmentos.forEach(function (seg) {
                const nivelSeg = parseInt(seg.dataset.nivel);
                seg.className = 'segmento-fortaleza h-1.5 flex-1 rounded-sm transition-colors duration-300 ';
                seg.className += nivelSeg <= nivel ? colores[nivel] : 'bg-slate-200';
            });

            etiqueta.textContent = etiquetas[nivel] ?? '';
            etiqueta.className   = 'etiqueta-fortaleza text-xs font-medium ' + (coloresEtiqueta[nivel] ?? 'text-slate-500');
        });
    });
}());
</script>
