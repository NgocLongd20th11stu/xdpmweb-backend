FROM php:8.2-cli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=$PORT