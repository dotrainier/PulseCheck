#!/bin/sh
set -e

echo "Fixing storage & cache permissions..."
chown -R www-data:www-data storage bootstrap/cache

echo "Running migrations..."
php artisan migrate --force

echo "Caching config and routes..."
php artisan config:cache
php artisan route:cache

echo "Creating storage symlink..."
php artisan storage:link --force

echo "Starting PHP-FPM..."
exec php-fpm