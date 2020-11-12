#!/bin/bash

if [ ! -e .env ]
 then cp .env.example .env
fi

if [ ! -e .env.testing ]
 then cp .env.testing.example .env.testing
fi

dbconnection=$(php artisan tinker --execute="echo (config('database.default'));")
if [ $dbconnection == "sqlite" ]
    db=$(php artisan tinker --execute="echo (config('database.connections.sqlite.database'));")
    then touch $db;
fi

chown -R www-data:www-data .
composer install
php artisan key:generate
php artisan migrate

php-fpm
