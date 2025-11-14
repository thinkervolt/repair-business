FROM composer:2.0 AS build
WORKDIR /app
COPY . /app
RUN composer install

FROM php:8.1-apache
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
 && docker-php-ext-install pdo_mysql bcmath mbstring xml \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=build /app /var/www/

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY _container/apache.conf /etc/apache2/sites-available/000-default.conf

RUN chmod 775 -R /var/www/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite 
