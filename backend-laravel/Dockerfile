FROM php:8.2-fpm-alpine

# Instalar dependencias de sistema y extensiones
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

# Copiar código
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader \
    && chmod -R 775 storage bootstrap/cache

# Copiar configuración de Nginx
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Copiar configuración de Supervisor para manejar php-fpm + nginx
COPY ./supervisord.conf /etc/supervisord.conf

EXPOSE 80

# Ejecutar supervisor (levanta php-fpm y nginx juntos)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
