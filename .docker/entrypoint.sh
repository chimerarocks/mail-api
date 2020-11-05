#!/bin/bash

if [ ! -e .env ]
 then cp .env.example .env
fi

chown -R www-data:www-data .
composer install
php artisan key:generate
php artisan migrate

php-fpm
