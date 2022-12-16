FROM php:8.1

RUN apt-get update && apt-get install -y --no-install-recommends curl=7.* git=1:2.* libzip-dev=1.* zip=3.* \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer