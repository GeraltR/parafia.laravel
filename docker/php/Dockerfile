FROM php:8.3-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
        opcache

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY conf.d/local.ini /usr/local/etc/php/conf.d/zzz-local.ini
COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/zzz-xdebug.ini

WORKDIR /var/www/html
