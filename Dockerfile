FROM php:8.2-cli

LABEL maintainer="David Locke"

RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    libzip-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN docker-php-ext-install zip

WORKDIR /app

COPY . /app

RUN cd ./tools && composer install --optimize-autoloader --no-dev


CMD [ "php", "tools/tools" ]