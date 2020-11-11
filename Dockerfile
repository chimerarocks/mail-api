FROM php:7.3.6-fpm-alpine3.9

RUN apk add --no-cache php7-pear php7-dev gcc musl-dev make
RUN apk add --no-cache shadow openssl bash mysql-client nodejs npm git
RUN pecl install redis \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable redis

RUN touch /home/www-data/.bashrc | echo "PS1='\w\$ '" >> /home/www-data/.bashrc

ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data

WORKDIR /var/www

RUN rm -rf /var/www/html && ln -s public html

RUN chown -R www-data:www-data /var/www

USER www-data

EXPOSE 9000
