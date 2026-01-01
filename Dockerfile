FROM composer:2.9 AS composer

COPY ./ /app
WORKDIR /app

# Run composer to install dependencies
RUN composer install \
  --optimize-autoloader \
  --no-interaction \
  --no-progress \
  --no-dev

FROM alpine:3.23.2

# Install packages
RUN apk add --no-cache \
  bash \
  curl \
  php83 \
  php83-curl \
  php83-ctype \
  php83-mbstring \
  php83-openssl \
  php83-phar \
  php83-xml \
  php83-xmlreader

# Copy app folder from composer build stage
COPY --from=composer /app /app

# Move daemon bash script
RUN mv /app/docker/scripts/daemon.sh /app/daemon.sh && rm -r /app/docker

# Create symlink for php
RUN ln -s /usr/bin/php82 /usr/bin/php

# Create cache folder
RUN mkdir /app/cache

WORKDIR /app
CMD [ "bash", "./daemon.sh" ]
