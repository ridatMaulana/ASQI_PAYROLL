FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    nginx \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    gd \
    pdo \
    pdo_mysql \
    zip \
    bcmath

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin semua file aplikasi terlebih dahulu
COPY . .

# Sebelum menjalankan composer, pastikan direktori yang diperlukan ada
# dan dapat ditulis oleh pengguna web server (www-data).
# Ini penting karena skrip composer (seperti package:discover) perlu menulis di sini.
RUN mkdir -p /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Sekarang jalankan composer install dengan aman
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Atur ulang perizinan sekali lagi untuk memastikan semuanya benar setelah instalasi
RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

EXPOSE 80

CMD ["php-fpm"]