# Bitman Vault

Bitman Vault es una plataforma web para la gestión centralizada y segura de credenciales, notas confidenciales, llaves SSH, secretos y archivos cifrados. Construida sobre Laravel y una arquitectura SPA impulsada por Alpine.js y Tailwind CSS, la aplicación utiliza autenticación mediante tokens JWT (JSON Web Tokens) para garantizar un acceso sin estado y seguro a la información.

## Funcionalidades del Sistema

### 1. Bóveda Principal de Secretos
- **Gestión de Credenciales (Logins):** Almacenamiento organizado de usuarios, contraseñas y sitios web asociados.
- **Tarjetas de Crédito y Débito:** Registro cifrado de información financiera y datos bancarios.
- **Notas Seguras:** Almacenamiento de información textual confidencial.
- **Llaves SSH:** Custodia de llaves privadas y públicas para acceso a servidores.
- **Códigos MFA (2FA):** Generación y almacenamiento de códigos de autenticación de doble factor.

### 2. Gestión de Archivos Cifrados
- **Almacenamiento Seguro:** Carga y administración de documentos y archivos con cifrado en reposo.

### 3. Compartición Segura (Share & Send)
- **Envío de Notas y Archivos:** Compartición temporal de información sensible mediante enlaces protegidos.
- **Control de Accesos:** Configuración de reglas de caducidad y límites de acceso para elementos compartidos.

### 4. Seguridad Avanzada
- **Interruptor de Emergencia (Dead Man Switch):** Mecanismo de transferencia de acceso o purga de datos en caso de inactividad prolongada del usuario.

### 5. Autenticación y Arquitectura
- **Autenticación mediante JWT:** Rutas de API protegidas mediante encabezados `Authorization: Bearer <token>`.
- **Arquitectura Limpia:** Separación de capas de dominio, aplicación e infraestructura basada en principios DDD y estándares PSR-12.

---

## Requisitos del Sistema

Para el despliegue en un entorno Linux de producción, se requieren los siguientes componentes:

- **Sistema Operativo:** Ubuntu Server 22.04 LTS / Debian 12 o superior.
- **Lenguaje:** PHP >= 8.2 con extensiones: `pdo`, `pdo_mysql` (o `pdo_sqlite`), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`.
- **Gestor de Paquetes PHP:** Composer >= 2.5.
- **Entorno de Ejecución JS:** Node.js >= 18.x y NPM >= 9.x.
- **Servidor Web:** Nginx o Apache HTTP Server.
- **Base de Datos:** MySQL >= 8.0, PostgreSQL >= 14, o SQLite 3.

---

## Guía de Compilación y Despliegue en Servidor Linux

A continuación se detalla el procedimiento para instalar, compilar y servir la aplicación en un entorno de producción Linux (ejemplo para servidor Nginx y PHP-FPM en Ubuntu/Debian).

### Paso 1: Clonar el Repositorio

Ubíquese en el directorio web de su servidor y clone el código fuente:

```bash
cd /var/www
git clone <URL_DEL_REPOSITORIO> bitman-app-php
cd bitman-app-php
```

### Paso 2: Instalar Dependencias de PHP

Ejecute Composer omitiendo dependencias de desarrollo y optimizando el autoloader:

```bash
composer install --no-dev --optimize-autoloader
```

### Paso 3: Instalar y Compilar Recursos Frontend

Instale las dependencias de Node.js y ejecute la compilación de producción con Vite:

```bash
npm install
npm run build
```

### Paso 4: Configuración del Archivo de Entorno

Cree el archivo `.env` a partir de la plantilla y configure las variables de entorno principales:

```bash
cp .env.example .env
```

Generar la clave de encriptación de la aplicación Laravel:

```bash
php artisan key:generate
```

Generar la clave secreta para la firma de tokens JWT:

```bash
php artisan jwt:secret
```

Edite el archivo `.env` para ajustar la conexión a la base de datos y la URL de la aplicación:

```ini
APP_NAME="Bitman Vault"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bitman_db
DB_USERNAME=bitman_user
DB_PASSWORD=contrasena_segura
```

### Paso 5: Migración de Base de Datos

Ejecute las migraciones de la base de datos en modo producción:

```bash
php artisan migrate --force
```

### Paso 6: Optimización de Caché de Laravel

Optimice las configuraciones, rutas y vistas para un rendimiento de producción:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 7: Permisos de Directorios

Asigne la propiedad del directorio al usuario del servidor web (`www-data`) y configure los permisos requeridos para almacenamiento y caché:

```bash
sudo chown -R www-data:www-data /var/www/bitman-app-php
sudo chmod -R 775 /var/www/bitman-app-php/storage /var/www/bitman-app-php/bootstrap/cache
```

---

## Configuración del Servidor Web (Nginx)

Cree un archivo de configuración de Nginx en `/etc/nginx/sites-available/bitman`:

```nginx
server {
    listen 80;
    server_name tudominio.com;
    root /var/www/bitman-app-php/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Habilite el sitio y reinicie el servicio de Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/bitman /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## Licencia

Este proyecto es software privado desarrollado para la gestión segura de información sensible. Todos los derechos reservados.
