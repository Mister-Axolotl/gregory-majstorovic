FROM composer:2@sha256:4d71c3c2109c61d5415544264b59ad4087e4c5b7244481723664138fd36d5040 AS builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader

FROM php:8.4-fpm-alpine@sha256:5992f8b7433fe7fa96dfbf67746c86d6c41bc91e686eac38fe531c72a02e40e4
WORKDIR /app

RUN apk add --no-cache libpq fcgi \
    && apk add --no-cache --virtual .build-deps postgresql-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo_pgsql \
    && apk del .build-deps

COPY docker/zz-ping.conf /usr/local/etc/php-fpm.d/

COPY --from=builder /app/vendor /app/vendor
COPY src /app/src
COPY public /app/public

RUN chown -R www-data:www-data /app
USER www-data

EXPOSE 9000

HEALTHCHECK --interval=10s --timeout=3s --start-period=10s --retries=3 \
    CMD SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
    cgi-fcgi-bind -connect 127.0.0.1:9000