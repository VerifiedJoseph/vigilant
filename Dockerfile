FROM composer:2.9.5@sha256:698d3801b2a622ace460c4743c781282fcbcb733a4cbf8b31c44731e846585e8 AS composer

COPY ./ /app
WORKDIR /app

# Run composer to install dependencies
RUN composer install \
  --optimize-autoloader \
  --no-interaction \
  --no-progress \
  --no-dev

FROM alpine:3.23.4@sha256:5b10f432ef3da1b8d4c7eb6c487f2f5a8f096bc91145e68878dd4a5019afde11

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
RUN ln -s /usr/bin/php83 /usr/bin/php

# Create cache folder
RUN mkdir /app/cache

WORKDIR /app
CMD [ "bash", "./daemon.sh" ]
