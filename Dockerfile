FROM alpine:3.16.2

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
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create app folder
RUN mkdir /app

# Add application
COPY ./ /app

WORKDIR /app

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress

CMD [ "php", "./daemon.php" ]
