# The first stage: builder
FROM php:8.2-cli as builder

LABEL maintainer="David Locke"

RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip

WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Copy your Composer files.
COPY ./tools/composer.json ./tools/composer.lock /app/tools/

# Install dependencies.
RUN cd /app/tools && composer install

# The second stage: the final Docker image
FROM php:8.2-cli

LABEL maintainer="David Locke"

WORKDIR /app

# Copy the vendor directory from the builder image
COPY --from=builder /app/tools/vendor /app/tools/vendor

# Copy the rest of your application code
COPY . /app

CMD [ "php", "tools/tools boot" ]
