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
    nodejs \
    npm

# PHP extensions (PostgreSQL driver)
RUN docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP dependencies
RUN composer install

# Frontend dependencies
RUN npm install

# Build Vue (IMPORTANT for production-like run)
RUN npm run build

EXPOSE 9000

CMD ["php-fpm"]