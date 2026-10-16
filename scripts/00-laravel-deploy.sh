#!/usr/bin/env bash
set -e

echo "Instalando dependencias de Composer..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --working-dir=/var/www/html

echo "Cacheando configuracion y rutas..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

echo "Ejecutando migraciones y seeder inicial..."
php artisan migrate --force --no-interaction
php artisan db:seed --class=MarinaInitialSeeder --force --no-interaction
