FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_mysql

# ESGI-3IW-Docker-TP
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

WORKDIR /var/www

COPY init-laravel-3iw.sh /init-laravel-3iw.sh

RUN chmod +x /init-laravel-3iw.sh
