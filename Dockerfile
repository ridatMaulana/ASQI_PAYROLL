FROM php:8.2-fpm
RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip gd
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . /var/www/html
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
RUN php artisan key:generate

EXPOSE 9000
