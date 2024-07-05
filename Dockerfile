FROM composer:2.5 as build
WORKDIR /app
COPY . /app
RUN composer install

FROM php:7.4-rc-apache-buster
RUN docker-php-ext-install pdo pdo_mysql

COPY --from=build /app /var/www/
COPY cloud-run-files/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY cloud-run-files/.env.cloud-run /var/www/.env

RUN chmod 777 -R /var/www/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite 