
FROM php:8.3-apache

RUN apt-get update -y && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo_pgsql pgsql

RUN chown -R www-data:www-data /var/www/html

RUN a2enmod rewrite