FROM php:8.3-fpm

WORKDIR /app

COPY . .

# System dependencies (IMPORTANT: libpq-dev added for PostgreSQL)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    nodejs \
    npm

# PHP extensions (PostgreSQL driver)
RUN docker-php-ext-install pdo pdo_pgsql intl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Frontend dependencies & build Vue
RUN npm install && npm run build

# Storage permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]