FROM php:7.2-alpine

RUN echo '@edge http://dl-cdn.alpinelinux.org/alpine/edge/main' >> /etc/apk/repositories

RUN set -x \
    && apk add --no-cache --virtual .build-deps postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apk add --no-cache postgresql-libs \
    && apk del .build-deps

# todo: remove build dependencies
RUN set -x \
#    && apk add --no-cache rabbitmq-c@edge \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        rabbitmq-c-dev@edge \
    && pecl install amqp \
    && docker-php-ext-enable amqp
#    && apk del .build-deps

RUN docker-php-ext-install bcmath

VOLUME /app
WORKDIR /app
