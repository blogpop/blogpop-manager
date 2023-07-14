FROM php:8.2-cli

LABEL maintainer="David Locke"

RUN apt-get update && apt-get install -y \
    curl

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /app

COPY . /app

RUN cd ./tools && composer install --optimize-autoloader --no-dev


CMD [ "php", "tools/tools" ]