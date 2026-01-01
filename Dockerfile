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

# Move daemon bash script
RUN mv /app/docker/scripts/daemon.sh /app/daemon.sh && rm -r /app/docker

# Create symlink for php
RUN ln -s /usr/bin/php82 /usr/bin/php

# Create cache folder
RUN mkdir /app/cache

WORKDIR /app
CMD [ "bash", "./daemon.sh" ]
