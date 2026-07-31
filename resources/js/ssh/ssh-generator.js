/**
 * @file ssh-generator.js
 * @description Librería orientada a objetos (principios SOLID) para la generación
 * e intermediación de llaves SSH (RSA y ECDSA) utilizando la API nativa del navegador Web Crypto API.
 */

/**
 * Convierte una cadena Base64URL a un Uint8Array de bytes.
 * @param {string} base64url 
 * @returns {Uint8Array}
 */
function base64UrlToUint8Array(base64url) {
    let base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
    while (base64.length % 4) {
        base64 += '=';
    }
    const raw = atob(base64);
    const result = new Uint8Array(raw.length);
    for (let i = 0; i < raw.length; i++) {
        result[i] = raw.charCodeAt(i);
    }
    return result;
}

/**
 * Convierte un Uint8Array o ArrayBuffer a cadena Base64.
 * @param {ArrayBuffer|Uint8Array} buffer 
 * @returns {string}
 */
function bufferToBase64(buffer) {
    const bytes = buffer instanceof Uint8Array ? buffer : new Uint8Array(buffer);
    let binary = '';
    for (let i = 0; i < bytes.length; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return btoa(binary);
}

/**
 * Encapsula la estructura de datos que representa un entero de tamaño variable (mpint) en formato OpenSSH wire.
 * @param {Uint8Array} bytes 
 * @returns {Uint8Array}
 */
function encodeMpint(bytes) {
    // Eliminar ceros iniciales insignificantes
    let start = 0;
    while (start < bytes.length && bytes[start] === 0) {
        start++;
    }
    let slice = bytes.subarray(start);

    // Si el MSB (bit más significativo) es 1, se antepone un byte 0x00 para indicar número positivo
    const needsPadding = (slice[0] & 0x80) !== 0;
    const len = slice.length + (needsPadding ? 1 : 0);

    const result = new Uint8Array(4 + len);
    const view = new DataView(result.buffer);
    view.setUint32(0, len, false); // Big-endian

    if (needsPadding) {
        result[4] = 0x00;
        result.set(slice, 5);
    } else {
        result.set(slice, 4);
    }

    return result;
}

/**
 * Encapsula una cadena de texto en formato OpenSSH wire (longitud uint32 + bytes UTF-8).
 * @param {string|Uint8Array} input 
 * @returns {Uint8Array}
 */
function encodeString(input) {
    const bytes = typeof input === 'string' ? new TextEncoder().encode(input) : input;
    const result = new Uint8Array(4 + bytes.length);
    const view = new DataView(result.buffer);
    view.setUint32(0, bytes.length, false); // Big-endian
    result.set(bytes, 4);
    return result;
}

/**
 * @class OpenSSHFormatter
 * @description Responsable exclusivamente de formatear llaves criptográficas a los estándares OpenSSH y PEM.
 * Cumple con el Principio de Responsabilidad Única (SRP).
 */
export class OpenSSHFormatter {
    /**
     * Convierte una clave privada PKCS#8 en formato de bloque PEM.
     * @param {ArrayBuffer} privateKeyBuffer - Clave privada exportada en formato 'pkcs8'.
     * @returns {string} Clave privada formateada en PEM.
     */
    static formatPrivateKeyPem(privateKeyBuffer) {
        const base64 = bufferToBase64(privateKeyBuffer);
        const lines = base64.match(/.{1,64}/g) || [];
        return [
            '-----BEGIN PRIVATE KEY-----',
            ...lines,
            '-----END PRIVATE KEY-----'
        ].join('\n');
    }

    /**
     * Convierte un JWK de clave pública RSA en el formato OpenSSH `ssh-rsa AAAAB3... comment`.
     * @param {JsonWebKey} jwk 
     * @param {string} comment 
     * @returns {string}
     */
    static formatRsaPublicKey(jwk, comment = 'user@bitman-vault') {
        const eBytes = base64UrlToUint8Array(jwk.e);
        const nBytes = base64UrlToUint8Array(jwk.n);

        const typeEncoded = encodeString('ssh-rsa');
        const eEncoded = encodeMpint(eBytes);
        const nEncoded = encodeMpint(nBytes);

        const totalLen = typeEncoded.length + eEncoded.length + nEncoded.length;
        const combined = new Uint8Array(totalLen);

        combined.set(typeEncoded, 0);
        combined.set(eEncoded, typeEncoded.length);
        combined.set(nEncoded, typeEncoded.length + eEncoded.length);

        const base64Key = bufferToBase64(combined);
        return `ssh-rsa ${base64Key} ${comment}`.trim();
    }

    /**
     * Convierte un JWK de clave pública ECDSA en formato OpenSSH `ecdsa-sha2-nistp256 AAAAE... comment`.
     * @param {JsonWebKey} jwk 
     * @param {string} comment 
     * @returns {string}
     */
    static formatEcdsaPublicKey(jwk, comment = 'user@bitman-vault') {
        const curveMap = {
            'P-256': { sshType: 'ecdsa-sha2-nistp256', curveName: 'nistp256' },
            'P-384': { sshType: 'ecdsa-sha2-nistp384', curveName: 'nistp384' },
            'P-521': { sshType: 'ecdsa-sha2-nistp521', curveName: 'nistp521' }
        };

        const config = curveMap[jwk.crv];
        if (!config) {
            throw new Error(`Curva elíptica no soportada: ${jwk.crv}`);
        }

        const xBytes = base64UrlToUint8Array(jwk.x);
        const yBytes = base64UrlToUint8Array(jwk.y);

        // Punto EC no comprimido (0x04 + X + Y)
        const qBytes = new Uint8Array(1 + xBytes.length + yBytes.length);
        qBytes[0] = 0x04;
        qBytes.set(xBytes, 1);
        qBytes.set(yBytes, 1 + xBytes.length);

        const typeEncoded = encodeString(config.sshType);
        const curveEncoded = encodeString(config.curveName);
        const qEncoded = encodeString(qBytes);

        const combined = new Uint8Array(typeEncoded.length + curveEncoded.length + qEncoded.length);
        combined.set(typeEncoded, 0);
        combined.set(curveEncoded, typeEncoded.length);
        combined.set(qEncoded, typeEncoded.length + curveEncoded.length);

        const base64Key = bufferToBase64(combined);
        return `${config.sshType} ${base64Key} ${comment}`.trim();
    }
}

/**
 * @interface ICryptoKeyStrategy
 * Interfaz/Contrato para las estrategias de generación criptográfica (Principio de Segregación de Interfaces ISP y Liskov LSP).
 */

/**
 * @class RSAKeyStrategy
 * Estrategia para generación de claves RSA (2048, 4096 bits).
 */
export class RSAKeyStrategy {
    /**
     * @param {number} modulusLength - 2048 o 4096
     */
    constructor(modulusLength = 2048) {
        this.modulusLength = parseInt(modulusLength, 10);
    }

    async generate() {
        const keyPair = await window.crypto.subtle.generateKey(
            {
                name: 'RSASSA-PKCS1-v1_5',
                modulusLength: this.modulusLength,
                publicExponent: new Uint8Array([1, 0, 1]), // 65537
                hash: 'SHA-256'
            },
            true,
            ['sign', 'verify']
        );

        const privateKeyBuffer = await window.crypto.subtle.exportKey('pkcs8', keyPair.privateKey);
        const publicKeyJwk = await window.crypto.subtle.exportKey('jwk', keyPair.publicKey);

        return { privateKeyBuffer, publicKeyJwk, type: 'RSA' };
    }
}

/**
 * @class ECDSAKeyStrategy
 * Estrategia para generación de claves ECDSA (P-256, P-384, P-521).
 */
export class ECDSAKeyStrategy {
    /**
     * @param {string} namedCurve - 'P-256' | 'P-384' | 'P-521'
     */
    constructor(namedCurve = 'P-256') {
        this.namedCurve = namedCurve;
    }

    async generate() {
        const keyPair = await window.crypto.subtle.generateKey(
            {
                name: 'ECDSA',
                namedCurve: this.namedCurve
            },
            true,
            ['sign', 'verify']
        );

        const privateKeyBuffer = await window.crypto.subtle.exportKey('pkcs8', keyPair.privateKey);
        const publicKeyJwk = await window.crypto.subtle.exportKey('jwk', keyPair.publicKey);

        return { privateKeyBuffer, publicKeyJwk, type: 'ECDSA' };
    }
}

/**
 * @class CryptoStrategyFactory
 * Fábrica para la creación de estrategias de generación según la configuración elegida (Principio de Abierto/Cerrado OCP).
 */
export class CryptoStrategyFactory {
    /**
     * Crea una estrategia de generación de llaves.
     * @param {Object} options 
     * @returns {RSAKeyStrategy|ECDSAKeyStrategy}
     */
    static createStrategy(options = {}) {
        const algorithm = (options.algorithm || 'RSA').toUpperCase();

        if (algorithm === 'RSA') {
            return new RSAKeyStrategy(options.modulusLength || 2048);
        } else if (algorithm === 'ECDSA') {
            return new ECDSAKeyStrategy(options.namedCurve || 'P-256');
        }

        throw new Error(`Algoritmo no soportado: ${algorithm}. Opciones válidas: RSA, ECDSA.`);
    }
}

/**
 * @class SSHKeyGeneratorService
 * Servicio principal (Fachada) parametrizable para la generación de claves SSH.
 * Sigue el Principio de Inversión de Dependencias (DIP).
 */
export class SSHKeyGeneratorService {
    /**
     * Genera un par de claves SSH parametrizable (Pública en OpenSSH y Privada en PEM).
     * 
     * @param {Object} options 
     * @param {string} [options.algorithm='RSA'] - 'RSA' o 'ECDSA'
     * @param {number} [options.modulusLength=2048] - 2048 o 4096 (solo para RSA)
     * @param {string} [options.namedCurve='P-256'] - 'P-256', 'P-384', 'P-521' (solo para ECDSA)
     * @param {string} [options.comment='user@bitman-vault'] - Comentario para la clave pública
     * 
     * @returns {Promise<{ privateKeyPem: string, publicKeyOpenSSH: string, algorithm: string }>}
     */
    static async generate(options = {}) {
        const comment = options.comment || 'user@bitman-vault';
        const strategy = CryptoStrategyFactory.createStrategy(options);

        // 1. Ejecutar estrategia de generación criptográfica nativa
        const { privateKeyBuffer, publicKeyJwk, type } = await strategy.generate();

        // 2. Formatear la clave privada a PEM (PKCS#8)
        const privateKeyPem = OpenSSHFormatter.formatPrivateKeyPem(privateKeyBuffer);

        // 3. Formatear la clave pública a formato OpenSSH
        let publicKeyOpenSSH = '';
        if (type === 'RSA') {
            publicKeyOpenSSH = OpenSSHFormatter.formatRsaPublicKey(publicKeyJwk, comment);
        } else if (type === 'ECDSA') {
            publicKeyOpenSSH = OpenSSHFormatter.formatEcdsaPublicKey(publicKeyJwk, comment);
        }

        return {
            privateKeyPem,
            publicKeyOpenSSH,
            algorithm: type
        };
    }
}
