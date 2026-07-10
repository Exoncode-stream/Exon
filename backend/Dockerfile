FROM php:8.3-apache

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80
