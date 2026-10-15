FROM richarvey/nginx-php-fpm:3.1.6

# Instalar Node.js y NPM en Alpine Linux para compilar Tailwind CSS / Vite
RUN apk add --update nodejs npm

COPY . /var/www/html

# Compilar los estilos y assets de frontend en producción
WORKDIR /var/www/html
RUN npm install && npm run build && rm -rf node_modules

# Variables de entorno requeridas por richarvey/nginx-php-fpm en Render
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Configuración de Laravel en producción
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
