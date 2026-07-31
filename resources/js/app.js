import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import jQuery from 'jquery';
window.$ = jQuery;
window.jQuery = jQuery;

import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

import { LoginEvents } from './events/events-login.js';
window.LoginEvents = LoginEvents;

import { Dialog } from './dialog.js';
window.Dialog = Dialog;

// Configuración global de Toastr para mantener consistencia en todo el proyecto
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-bottom-right", // O toast-top-right, lo que prefieras
    "timeOut": "3000",
};

// Exponer toastr globalmente para que Blade o cualquier script inline pueda acceder
window.toastr = toastr;

// Disparar las notificaciones basándose en los atributos de datos del body
const body = document.body;
if (body) {
    if (body.dataset.sessionSuccess) toastr.success(body.dataset.sessionSuccess);
    if (body.dataset.sessionError) toastr.error(body.dataset.sessionError);
    if (body.dataset.sessionInfo) toastr.info(body.dataset.sessionInfo);
    if (body.dataset.sessionWarning) toastr.warning(body.dataset.sessionWarning);
}
