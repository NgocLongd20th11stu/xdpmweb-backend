FROM php:8.2-cli

WORKDIR /var/www

# Cài đặt các phụ thuộc hệ thống
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# Copy composer từ image chính thức
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy toàn bộ code
COPY . .

# Cài đặt thư viện PHP
RUN composer install --no-dev --optimize-autoloader

# Phân quyền cho thư mục storage và bootstrap/cache (Rất quan trọng trên Linux/Docker)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Lệnh khởi chạy
# Sử dụng sh -c để đảm bảo biến $PORT được shell giải thích đúng giá trị
CMD sh -c "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT"