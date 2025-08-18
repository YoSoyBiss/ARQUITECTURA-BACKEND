FROM php:8.2-fpm-alpine

# Instalar dependencias de sistema y extensiones de PHP
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    unzip \
    bash \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql bcmath

# Instalar Composer
COPY --from=composer:2.7.0 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Copiar código de la app
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader \
    && chmod -R 775 storage bootstrap/cache

# Copiar configuración de Nginx y Supervisor
COPY ./nginx.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisord.conf

EXPOSE 80

# Supervisor maneja php-fpm + nginx al mismo tiempo
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
