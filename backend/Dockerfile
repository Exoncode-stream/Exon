FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80
