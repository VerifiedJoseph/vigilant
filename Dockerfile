FROM alpine:3.16.2
FROM composer:2.4 AS composer

# Install packages
RUN apk add --no-cache \
  curl \
  php81 \
  php81-curl \
  php81-dom \
  php81-intl \
  php81-mbstring \
  php81-openssl \
  php81-phar \
  php81-xml \
  php81-xmlreader

# Create symlink for `php`
RUN ln -s /usr/bin/php81 /usr/bin/php

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Add application
COPY ./ /app

WORKDIR /app

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress

CMD [ "php", "./daemon.php" ]
