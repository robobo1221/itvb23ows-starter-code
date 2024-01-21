FROM php:latest

RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    unzip


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /app

COPY ./src /app/src
COPY ./tests /app/tests
COPY ./composer.json /app/composer.json
COPY ./composer.lock /app/composer.lock
COPY ./phpunit.xml /app/phpunit.xml

RUN composer install --no-interaction

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "./src"]