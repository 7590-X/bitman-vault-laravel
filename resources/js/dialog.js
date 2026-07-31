import { svgs } from './icons.js';

export const Dialog = {
    confirm: function ({ 
        title, 
        text, 
        confirmText = 'Sí, confirmar', 
        cancelText = 'Cancelar', 
        type = 'warning' 
    }) {
        return new Promise((resolve) => {
            // Definición de colores según el tipo
            const colors = {
                warning: { icon: 'text-amber-500 bg-amber-100 dark:bg-amber-500/20', btn: 'bg-amber-500 hover:bg-amber-600 text-white' },
                danger: { icon: 'text-red-500 bg-red-100 dark:bg-red-500/20', btn: 'bg-red-600 hover:bg-red-700 text-white' },
                info: { icon: 'text-blue-500 bg-blue-100 dark:bg-blue-500/20', btn: 'bg-blue-600 hover:bg-blue-700 text-white' },
                success: { icon: 'text-emerald-500 bg-emerald-100 dark:bg-emerald-500/20', btn: 'bg-emerald-600 hover:bg-emerald-700 text-white' }
            };
            const currentType = colors[type] || colors.info;


            // Contenedor principal y overlay
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 z-[100] flex items-center justify-center opacity-0 transition-opacity duration-300';
            
            // Fondo difuminado (blur)
            const backdrop = document.createElement('div');
            backdrop.className = 'absolute inset-0 bg-slate-900/40 backdrop-blur-sm';
            
            // Ventana del modal
            const modal = document.createElement('div');
            modal.className = 'relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4 transform scale-95 transition-transform duration-300 border border-slate-200 dark:border-slate-800 text-center flex flex-col items-center';
            
            modal.innerHTML = `
                <div class="flex items-center justify-center w-12 h-12 rounded-full mb-4 ${currentType.icon}">
                    ${svgs[type] || svgs.info}
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">${title}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">${text}</p>
                <div class="flex gap-3 w-full">
                    <button id="btn-cancel" class="flex-1 py-2.5 px-4 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-500">
                        ${cancelText}
                    </button>
                    <button id="btn-confirm" class="flex-1 py-2.5 px-4 ${currentType.btn} rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        ${confirmText}
                    </button>
                </div>
            `;

            overlay.appendChild(backdrop);
            overlay.appendChild(modal);
            document.body.appendChild(overlay);

            // Animar entrada
            requestAnimationFrame(() => {
                overlay.classList.remove('opacity-0');
                modal.classList.remove('scale-95');
                modal.classList.add('scale-100');
            });

            // Función para cerrar
            const close = (result) => {
                overlay.classList.add('opacity-0');
                modal.classList.remove('scale-100');
                modal.classList.add('scale-95');
                setTimeout(() => {
                    overlay.remove();
                    resolve({ isConfirmed: result });
                }, 300);
            };

            // Event Listeners
            modal.querySelector('#btn-cancel').onclick = () => close(false);
            modal.querySelector('#btn-confirm').onclick = () => close(true);
            backdrop.onclick = () => close(false);
            
            // Cerrar con Escape
            const handleEscape = (e) => {
                if (e.key === 'Escape') {
                    document.removeEventListener('keydown', handleEscape);
                    close(false);
                }
            };
            document.addEventListener('keydown', handleEscape);
        });
    }
};
