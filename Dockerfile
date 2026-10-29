FROM composer:2.10.3@sha256:d8f6343d3fae98107426bc49163ccad46ef85aabd4a27d80a74401fab4aba332 AS composer

COPY ./ /app
WORKDIR /app

# Run composer to install dependencies
RUN composer install \
  --optimize-autoloader \
  --no-interaction \
  --no-progress \
  --no-dev

FROM alpine:3.24.3@sha256:b3413572228e34bd1c9245f5565e6cd127005655e8a7899aef33d8e83255c9e6

# Install packages
RUN apk add --no-cache \
  tini \
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
ENTRYPOINT ["tini", "--"]
CMD [ "bash", "./daemon.sh" ]
