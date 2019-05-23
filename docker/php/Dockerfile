FROM php:7.2.12-fpm-alpine

MAINTAINER Andrea Cristaudo <andrea.cristaudo@gmail.com>

RUN echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/memory-limit.ini" \
    && echo "date.timezone=${PHP_TIMEZONE:-UTC}" > "$PHP_INI_DIR/conf.d/date_timezone.ini"

RUN apk add --no-cache --update --virtual .php-ext-build-deps \
        autoconf \
        binutils \
        dpkg \
        dpkg-dev \
        file \
        g++ \
        gcc \
        gettext-dev \
        icu-dev \
        isl \
        libatomic \
        libbz2 \
        libmagic \
        libpcre16 \
        libpcre32 \
        libpcrecpp \
        libtool \
        m4 \
        make \
        mpc1 \
        mpfr3 \
        musl-utils \
        musl-dev \
        pcre-dev \
        perl \
        re2c \
        zlib-dev \
    && apk add --no-cache --virtual .persistent-deps \
        gettext \
        icu-libs \
    && docker-php-ext-configure bcmath --enable-bcmath \
    && docker-php-ext-configure gettext \
    && docker-php-ext-configure intl --enable-intl \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-configure zip --enable-zip \
    && docker-php-ext-install -j$(nproc) \
                    bcmath \
                    gettext \
                    intl \
                    opcache \
                    pdo_mysql \
                    zip \
    && rm -rf /tmp/* \
    ; rm -rf /var/cache/apk/* \
    ; pecl clear-cache \
    ; docker-php-source delete \
    ; apk del .php-ext-build-deps

WORKDIR /app

RUN deluser --remove-home www-data \
  && addgroup -S www-data -g 1000 \
  && adduser -S -G www-data -u 1000 www-data

COPY --from=composer:1.8.4 /usr/bin/composer /usr/bin/composer

USER www-data
