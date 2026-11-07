# Stage 1: install PHP dependencies. Only the result is kept, not Composer.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Dependency layer is cached until composer.lock changes.
# PHP extensions live in the final image, so they are not checked here.
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
    --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts --ignore-platform-reqs

# Stage 2: build the front-end assets. Node never reaches the final image.
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN npm run build

# Stage 3: the image that actually runs.
FROM php:8.2-apache AS app

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
# gd and zip are needed by the PDF and Excel packages.
RUN install-php-extensions pdo_mysql gd zip bcmath intl pcntl opcache

# Serve public/ on port 8080, so Apache can run without root.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN a2enmod rewrite \
    && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!Listen 80!Listen 8080!' /etc/apache2/ports.conf \
    && sed -ri 's!:80>!:8080>!' /etc/apache2/sites-available/*.conf \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html
COPY --from=vendor --chown=www-data:www-data /app ./
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

RUN mkdir -p storage/app/public storage/logs public/uploads bootstrap/cache \
        storage/framework/cache/data storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads

USER www-data
RUN php artisan package:discover --ansi

EXPOSE 8080
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://localhost:8080/up || exit 1
