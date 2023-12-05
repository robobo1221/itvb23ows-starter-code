FROM php:latest

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /app

COPY . /app

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]