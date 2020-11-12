FROM php:7.3.6-fpm-alpine3.9 as builder

RUN apk add --no-cache php7-pear php7-dev gcc musl-dev make shadow openssl bash mysql-client git
RUN pecl install apcu \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable apcu

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
RUN rm -rf /var/www/html \
            && ln -s public html
COPY . /var/www

RUN composer install \
        && php artisan key:generate \
        && php artisan cache:clear \
        && chmod -R 775 storage

FROM php:7.3.6-fpm-alpine3.9

RUN apk add --no-cache php7-pear php7-dev gcc musl-dev make shadow openssl bash mysql-client git
RUN pecl install apcu \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable apcu

WORKDIR /var/www
RUN rm -rf /var/www/html \
            && ln -s public html
COPY --from=builder /var/www .

EXPOSE 8080
ENTRYPOINT ["php" ,"-S", "0.0.0.0:8080", "-t", "public/"]
