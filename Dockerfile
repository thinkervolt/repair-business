FROM composer:2.0 AS vendor
WORKDIR /app
COPY . /app
RUN mkdir -p /app/bootstrap/cache \
        /app/storage/app/public \
        /app/storage/framework/cache/data \
        /app/storage/framework/sessions \
        /app/storage/framework/views \
        /app/storage/logs \
 && composer install --no-progress --prefer-dist

FROM node:20-alpine AS client-build
WORKDIR /app
COPY client/package.json client/package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY client/ ./
RUN npm run build

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

COPY --from=vendor /app /var/www/

COPY --from=client-build /app/dist/ /var/www/public/

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY _container/apache.conf /etc/apache2/sites-available/000-default.conf

RUN chmod 775 -R /var/www/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite