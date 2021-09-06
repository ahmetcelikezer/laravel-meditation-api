FROM php:8.0-fpm

RUN apt-get update && apt-get install -y zip unzip zlib1g-dev libicu-dev libzip-dev

RUN pecl install xdebug

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable pdo_mysql xdebug

WORKDIR /var/www/app

EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
