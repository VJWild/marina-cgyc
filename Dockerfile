# Etapa 1: Compilar Tailwind CSS y assets con Node 22 (compatible con Vite 8)
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Etapa 2: Servidor PHP 8.3 con PostgreSQL y extensiones de Laravel 11
FROM php:8.3-cli-alpine

# Instalar dependencias del sistema y driver de PostgreSQL (pdo_pgsql)
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath opcache

# Traer Composer oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar código del proyecto y los estilos ya compilados en la Etapa 1
COPY . .
COPY --from=assets /app/public/build ./public/build

# Instalar paquetes de Laravel para producción
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Asegurar permisos en carpetas de caché y logs
RUN chmod -R 775 storage bootstrap/cache

# Variables de producción
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

# Puerto estándar de Render
EXPOSE 10000

# Al iniciar el contenedor: limpia/cachea configuración, corre migraciones + seeder y levanta el servidor
CMD sh -c "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force --no-interaction && php artisan db:seed --class=MarinaInitialSeeder --force --no-interaction && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"
