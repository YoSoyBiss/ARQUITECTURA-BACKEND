FROM php:8.2-fpm-alpine

# Instala las dependencias del sistema y extensiones de PHP
RUN apk add --no-cache \
    nginx \
    $PHPIZE_DEPS \
    && docker-php-ext-install \
    pdo pdo_mysql \
    && docker-php-ext-enable \
    pdo_mysql

# Instala Composer
COPY --from=composer:2.7.0 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

# Copia el código de la aplicación
COPY . .

# Instala las dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Configura los permisos de escritura
RUN chmod -R 775 storage bootstrap/cache

# Configuración de Nginx (para servir tu app)
COPY --from=laravel/sail:latest /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
EXPOSE 8000
CMD ["php-fpm", "--allow-to-run-as-root", "-D"] && nginx -g "daemon off;"