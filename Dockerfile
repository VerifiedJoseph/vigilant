FROM composer:2.7.6 AS composer

COPY ./ /app
WORKDIR /app

# Run composer to install dependencies
RUN composer install \
  --optimize-autoloader \
  --no-interaction \
  --no-progress \
  --no-dev

FROM alpine:3.20.0

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

WORKDIR /app
CMD [ "bash", "./daemon.sh" ]
