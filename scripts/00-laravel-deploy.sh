#!/usr/bin/env bash
echo "Instalando dependencias de Composer para producción..."
composer install --no-dev --working-dir=/var/www/html

echo "Generando caché de configuración, rutas y vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Ejecutando migraciones en PostgreSQL..."
php artisan migrate --force

echo "Cargando catálogo inicial de Zonas, Muelles y Embarcaciones..."
php artisan db:seed --class=MarinaInitialSeeder --force
