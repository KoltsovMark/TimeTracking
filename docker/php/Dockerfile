FROM composer:2.0 as composer

FROM php:7.4-fpm

RUN docker-php-ext-install pdo_mysql

RUN pecl install apcu

RUN apt-get update && \
apt-get install -y libzip-dev && \
apt-get install -y libpng-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-enable apcu
RUN docker-php-ext-install gd

WORKDIR /usr/src/app

COPY --chown=${LOCAL_USER} ./ /usr/src/app
RUN chown -R www-data /usr/src/app
RUN chmod -R 777 /usr/src/app/var/cache
RUN chmod -R 777 /usr/src/app/var/log
RUN chmod -R 777 /usr/src/app/config

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN PATH=$PATH:/usr/src/app/vendor/bin:bin

RUN composer install --no-scripts --prefer-dist \
  && rm -rf "$(composer config cache-dir)" "$(composer config data-dir)"

EXPOSE 3306