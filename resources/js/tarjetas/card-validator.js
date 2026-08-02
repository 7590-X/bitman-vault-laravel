/**
 * Card Validator Module
 * Proporciona verificación del Algoritmo de Luhn (Módulo 10),
 * detección de franquicias emisoras y validación de fecha de expiración.
 */

/**
 * Valida un número de tarjeta utilizando el Algoritmo de Luhn (Módulo 10).
 * @param {string|number} numero - Número de tarjeta (dígitos continuos).
 * @returns {boolean} True si el checksum de Luhn es válido.
 */
export function validarLuhn(numero) {
    if (!numero) return false;
    const str = String(numero).replace(/\D/g, '');
    if (str.length < 13 || str.length > 19) return false;

    let sum = 0;
    let shouldDouble = false;

    // Recorrer de derecha a izquierda
    for (let i = str.length - 1; i >= 0; i--) {
        let digit = parseInt(str.charAt(i), 10);

        if (shouldDouble) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }

        sum += digit;
        shouldDouble = !shouldDouble;
    }

    return (sum % 10 === 0);
}

/**
 * Detecta la franquicia de la tarjeta basándose en el prefijo (BIN/IIN).
 * @param {string|number} numero - Número de tarjeta.
 * @returns {{ clave: string, nombre: string }}
 */
export function detectarFranquicia(numero) {
    if (!numero) return { clave: 'desconocida', nombre: 'Tarjeta' };
    const str = String(numero).replace(/\D/g, '');

    // Visa: 4
    if (/^4/.test(str)) {
        return { clave: 'visa', nombre: 'Visa' };
    }

    // Mastercard: 51-55 o 2221-2720
    if (/^(5[1-5]|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)/.test(str)) {
        return { clave: 'mastercard', nombre: 'Mastercard' };
    }

    // American Express: 34 o 37
    if (/^3[47]/.test(str)) {
        return { clave: 'amex', nombre: 'American Express' };
    }

    // Discover: 6011, 622126-622925, 644-649, 65
    if (/^(6011|65|64[4-9]|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]))/.test(str)) {
        return { clave: 'discover', nombre: 'Discover' };
    }

    // Diners Club: 300-305, 36, 38
    if (/^(30[0-5]|36|38)/.test(str)) {
        return { clave: 'diners', nombre: 'Diners Club' };
    }

    // JCB: 3528-3589
    if (/^35(2[89]|[3-8][0-9])/.test(str)) {
        return { clave: 'jcb', nombre: 'JCB' };
    }

    return { clave: 'desconocida', nombre: 'Tarjeta' };
}

/**
 * Valida que la fecha de expiración (MM/YY o MM/YYYY) no haya caducado.
 * @param {string} fechaStr - Fecha en formato MM/YY o MM/YYYY.
 * @returns {boolean}
 */
export function validarFechaExpiracion(fechaStr) {
    if (!fechaStr || !/^\d{2}\/\d{2,4}$/.test(fechaStr.trim())) return false;
    const [mesStr, anioStr] = fechaStr.trim().split('/');
    const mes = parseInt(mesStr, 10);
    if (mes < 1 || mes > 12) return false;

    let anio = parseInt(anioStr, 10);
    if (anioStr.length === 2) {
        anio += 2000;
    }

    const hoy = new Date();
    const anioActual = hoy.getFullYear();
    const mesActual = hoy.getMonth() + 1; // 1-indexed

    if (anio < anioActual) return false;
    if (anio === anioActual && mes < mesActual) return false;

    return true;
}

/**
 * Servicio unificado de validación de tarjetas.
 */
export const CardValidator = {
    validarLuhn,
    detectarFranquicia,
    validarFechaExpiracion,
};

export default CardValidator;
