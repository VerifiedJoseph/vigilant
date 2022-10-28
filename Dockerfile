FROM composer:2.4 AS composer

# Copy application
COPY ./ /app

WORKDIR /app

# Run composer install to install the dependencies
RUN composer install \
  --optimize-autoloader \
  --no-interaction \
  --no-progress \
  --no-dev

FROM alpine:3.16.2

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

# Copy app folder composer build stage
COPY --from=composer /app /app

WORKDIR /app

CMD [ "php", "./daemon.php" ]
