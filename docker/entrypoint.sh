#!/bin/sh
set -e

echo "Waiting for PostgreSQL..."
until php artisan db:show > /dev/null 2>&1; do
    sleep 2
done

echo "Running migrations..."
php artisan migrate --force

echo "Caching config and routes..."
php artisan config:cache
php artisan route:cache

echo "Creating storage symlink..."
php artisan storage:link --force

echo "Starting PHP-FPM..."
exec php-fpm
