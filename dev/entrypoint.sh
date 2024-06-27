#!/bin/bash
composer install

if [ ! -f .env ]; then
    cp dev/.env.example .env
    php artisan key:generate
fi

if [ ! -f database.sqlite ]; then
    touch database/database.sqlite
fi

php artisan migrate
php artisan db:seed


php artisan serve --host=0.0.0.0 --port=8000


RUN tail -f /dev/null