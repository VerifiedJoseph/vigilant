ARG COMPOSER_VERSION=2.4
ARG ALPINE_VERSION=3.16.2
FROM composer:${COMPOSER_VERSION} AS composer

# Copy application
COPY ./ /app

WORKDIR /app

# Run composer install to install the dependencies
RUN composer install \
  --optimize-autoloader \
  --no-interaction \
  --no-progress \
  --no-dev

FROM alpine:${ALPINE_VERSION}

# Install packages
RUN apk add --no-cache \
  curl \
  php81 \
  php81-curl \
  php81-ctype \
  php81-mbstring \
  php81-openssl \
  php81-phar \
  php81-xml \
  php81-xmlreader

# Create symlink for `php`
RUN ln -s /usr/bin/php81 /usr/bin/php

# Copy app folder from composer build stage
COPY --from=composer /app /app

WORKDIR /app

CMD [ "php", "./daemon.php" ]
