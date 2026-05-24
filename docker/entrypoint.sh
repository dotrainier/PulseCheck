#!/bin/sh
set -e

echo "Waiting for PostgreSQL..."
until (echo > /dev/tcp/postgres/5432) 2>/dev/null; do
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
