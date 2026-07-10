FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    git \
    unzip \
    && docker-php-ext-install pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80
