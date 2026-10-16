# Etapa 1: Compilar Tailwind CSS y assets con Node 22
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Etapa 2: Servidor PHP 8.3 con PostgreSQL
FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
RUN chmod -R 775 storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

EXPOSE 10000

# Script de inicio seguro que espera a que la base de datos responda y arranca el servidor
CMD sh -c "php artisan config:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force --no-interaction && php artisan db:seed --class=MarinaInitialSeeder --force --no-interaction && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"
