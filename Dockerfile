FROM composer:2.8 AS composer

COPY ./ /app
WORKDIR /app

# Run composer to install dependencies
RUN composer install \
  --optimize-autoloader \
  --no-interaction \
  --no-progress \
  --no-dev

FROM alpine:3.22.0

# Install packages
RUN apk add --no-cache \
  bash \
  curl \
  php82 \
  php82-curl \
  php82-ctype \
  php82-mbstring \
  php82-openssl \
  php82-phar \
  php82-xml \
  php82-xmlreader

# Copy app folder from composer build stage
COPY --from=composer /app /app

# Copy daemon bash script
COPY /docker/scripts/daemon.sh /app/daemon.sh
RUN rm -r /app/docker

# Create symlink for php
RUN ln -s /usr/bin/php82 /usr/bin/php

# Create cache folder
RUN mkdir /app/cache

WORKDIR /app
CMD [ "bash", "./daemon.sh" ]
