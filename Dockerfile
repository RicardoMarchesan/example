FROM php:8.3-fpm-alpine3.20
LABEL authors="marchesan"

ARG user=construapp
ARG uid=1000

RUN apk --no-cache add \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq \
    postgresql-dev \
    openssl \
    bash \
    nodejs \
    npm \
    alpine-sdk \
    autoconf \
    librdkafka-dev \
    vim \
    nginx \
    oniguruma-dev \
    linux-headers \
    openrc

RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd sockets

RUN pecl install redis rdkafka xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis rdkafka xdebug

RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN adduser -D -u ${uid} -G www-data -h /home/${user} ${user} \
    && mkdir -p /home/${user}/.composer \
    && chown -R ${user}:www-data /home/${user}

COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN ln -s /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

USER ${user}



