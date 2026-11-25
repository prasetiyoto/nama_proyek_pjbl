# syntax=docker/dockerfile:1
#
# Image produksi Laravel untuk Render (PHP 8.4 + Apache).
# Render tidak punya runtime PHP bawaan, jadi aplikasi dijalankan sebagai Docker.
# Render menyuntikkan variabel PORT (bawaan 10000); Apache mendengarkan port itu.

############################################################
# Tahap 1: PHP + ekstensi + dependensi Composer
############################################################
FROM php:8.4-apache AS app

RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip libpq-dev libzip-dev \
 && docker-php-ext-install pdo_pgsql pdo_mysql zip opcache \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/* \
 && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Document root ke folder public/, .htaccess Laravel diizinkan, port mengikuti $PORT
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    PORT=10000
RUN sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
 && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
 && sed -ri 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf \
 && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf \
 && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
 && a2enconf servername

WORKDIR /var/www/html

# Dependensi dipasang lebih dulu agar layer ini di-cache selama composer.lock tidak berubah
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
 && composer dump-autoload --optimize --no-dev \
 && php artisan package:discover --ansi \
 && chown -R www-data:www-data storage bootstrap/cache

############################################################
# Tahap 2: build aset front-end (Vite + Tailwind) -> public/build
############################################################
FROM node:22-slim AS assets
WORKDIR /app

COPY package.json .npmrc ./
RUN npm install --no-audit --no-fund

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
# Tailwind memindai view pagination bawaan Laravel (lihat @source di resources/css/app.css)
COPY --from=app /var/www/html/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN mkdir -p storage/framework/views && npm run build

############################################################
# Tahap 3: image akhir
############################################################
FROM app AS runtime

COPY --from=assets /app/public/build ./public/build
COPY docker/start.sh /usr/local/bin/start-laravel
RUN chmod +x /usr/local/bin/start-laravel

EXPOSE 10000
CMD ["start-laravel"]
