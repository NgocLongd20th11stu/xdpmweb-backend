FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Tạo key cho Laravel (tránh lỗi nếu chưa có)
RUN php artisan key:generate || true

# Command khi container chạy
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT