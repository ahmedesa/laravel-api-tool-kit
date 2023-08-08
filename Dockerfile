FROM php:8.0-fpm-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apk add --update --no-cache libzip-dev icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    unzip \
    git

RUN apk add --no-cache --update --virtual buildDeps autoconf gcc make g++ zlib-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && pecl install igbinary \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-enable igbinary \
    && apk del buildDeps

RUN echo memory_limit = -1 >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

WORKDIR /var/www/app

EXPOSE 9000

CMD ["php-fpm"]
