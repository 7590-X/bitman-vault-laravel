# Stage 1: Compilación de Assets con Node.js
FROM node:20-alpine AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
ENV NODE_OPTIONS="--dns-result-order=ipv4first"
RUN npm run build

# Stage 2: Instalación de dependencias PHP con Composer
FROM composer:2 AS vendor-builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Stage 3: Imagen Final de Producción (PHP 8.4 FPM + Nginx + Supervisor)
FROM php:8.4-fpm-alpine

# Instalación de paquetes del sistema y dependencias de extensiones PHP
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    bash \
    curl

# Configuración e instalación de extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        bcmath \
        opcache \
        zip \
        gd \
        intl \
        mbstring \
        xml

# Copiar configuraciones personalizadas
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

# Copiar archivos del proyecto y artefactos compilados
COPY . /var/www/html
COPY --from=vendor-builder /app/vendor /var/www/html/vendor
COPY --from=assets-builder /app/public/build /var/www/html/public/build

# Permisos de almacenamiento y caché de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
