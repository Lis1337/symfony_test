FROM php:7.4.16-fpm-alpine3.13

MAINTAINER PartOS <partos.k@mail.ru>

RUN apk add --no-cache \
        libzip-dev \
        curl \
        wget \
        git \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev  \
        libxml2-dev \
        openssl-dev \
        libressl-dev \
        icu icu-dev \
        openssh \
    && docker-php-ext-install -j$(nproc) iconv mysqli pdo_mysql zip tokenizer xml pcntl\
    && docker-php-ext-install intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

RUN addgroup -S web && adduser -S web -D -u 1000 -h /home/web -G web

WORKDIR /var/www/

#RUN mkdir /home/web/ && chown -R 1000:1000 /home/web
USER web

CMD ["php-fpm"]
