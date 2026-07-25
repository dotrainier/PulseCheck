FROM php:8.3-fpm

WORKDIR /app

# System dependencies (libpq-dev for PostgreSQL)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpq-dev \
    nodejs \
    npm

# PHP extensions (PostgreSQL driver)
RUN docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Dependencies first — these layers only rebuild when the lockfiles change
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

COPY package.json package-lock.json ./
RUN npm install

# App code
COPY . .

# Finish Composer autoloader + build the Vue frontend
RUN composer dump-autoload --optimize \
    && npm run build

# Storage permissions baked at build time
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

# Runtime startup: re-fix permissions (queue/scheduler share this volume),
# run migrations, cache config/routes, link storage, then hand off to php-fpm
ENTRYPOINT chown -R www-data:www-data storage bootstrap/cache \
    && php artisan migrate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan storage:link --force \
    && exec php-fpm