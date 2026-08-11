# =========================================================
# Stage 1: Dependencias de Composer
# =========================================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# =========================================================
# Stage 2: Build de assets (Vite + Tailwind)
# =========================================================
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# =========================================================
# Stage 3: Imagen final de runtime (PHP-FPM sobre Alpine)
# =========================================================
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
        bash \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        zip \
        unzip \
        icu-dev \
        oniguruma-dev \
        mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_mysql \
        mbstring \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        exif

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache-custom.ini

WORKDIR /var/www

COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY . .

RUN rm -f bootstrap/cache/services.php bootstrap/cache/packages.php \
    && php artisan package:discover --ansi

RUN addgroup -g 1000 sgsi \
    && adduser -G sgsi -u 1000 -D sgsi \
    && chown -R sgsi:sgsi /var/www \
    && chmod -R 775 storage bootstrap/cache

USER sgsi

EXPOSE 9000

CMD ["php-fpm"]
