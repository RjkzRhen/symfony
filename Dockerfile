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

# Копирование проекта
COPY . /var/php/symfony_project

# Установка прав доступа
RUN chown -R www-data:www-data /var/php/symfony_project/var/log/
RUN chown -R www-data:www-data /var/php/symfony_project/var/cache/
RUN chmod -R 775 /var/php/symfony_project/var/log/
RUN chmod -R 775 /var/php/symfony_project/var/cache/

WORKDIR /var/php/symfony_project