FROM php:7.4-apache 

WORKDIR /app
RUN apt-get update && apt-get install -y
RUN apt-get install zip -y


RUN docker-php-ext-install pdo pdo_mysql mysqli

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh


ENTRYPOINT ["/entrypoint.sh"]