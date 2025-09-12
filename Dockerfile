FROM composer:2.0 AS build
WORKDIR /app
COPY . /app
RUN composer install

FROM php:7.4-rc-apache-buster
RUN docker-php-ext-install pdo pdo_mysql

COPY --from=build /app /var/www/

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY _container/apache.conf /etc/apache2/sites-available/000-default.conf

RUN chmod 777 -R /var/www/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite 
