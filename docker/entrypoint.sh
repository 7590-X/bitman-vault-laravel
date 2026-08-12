#!/bin/sh
set -e

echo "==> Cleaning stale bootstrap cache..."
rm -f /var/www/html/bootstrap/cache/config.php /var/www/html/bootstrap/cache/routes-*.php /var/www/html/bootstrap/cache/packages.php /var/www/html/bootstrap/cache/services.php

echo "==> Discovering packages & Optimizing Laravel cache..."
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

echo "==> Creating storage symlink..."
php artisan storage:link --force || true

echo "==> Executing database migrations..."
php artisan migrate --force

echo "==> Setting directory permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Starting application services..."
exec "$@"
