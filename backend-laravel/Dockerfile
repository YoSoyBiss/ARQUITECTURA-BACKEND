# Usamos PHP 8.2 FPM
FROM php:8.2-fpm-alpine

# Instalar dependencias del sistema y extensiones PHP
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    unzip \
    bash \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql bcmath

# Instalar Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Crear directorios que Nginx necesita
RUN mkdir -p /var/run /var/log/nginx \
    && chown -R root:root /var/run /var/log/nginx

# Definir directorio de trabajo
WORKDIR /var/www/html

# Copiar código de Laravel
COPY . .

# Instalar dependencias de Laravel y permisos
RUN composer install --no-dev --optimize-autoloader \
    && chmod -R 775 storage bootstrap/cache

# Copiar configuración de Nginx y Supervisor
COPY ./nginx.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisord.conf

# Exponer puerto 80
EXPOSE 80

# Ejecutar Supervisor para levantar php-fpm + nginx
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
