FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip
RUN chown -R www-data:www-data /var/php/symfony_project
WORKDIR /var/php/symfony_project
