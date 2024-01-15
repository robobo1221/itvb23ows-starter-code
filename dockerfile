FROM php:latest

RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    unzip


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /app

COPY . /app

RUN composer install --no-interaction

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "src"]