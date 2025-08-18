# PHP 8.2 FPM
FROM php:8.2-fpm-alpine

# Instalar dependencias y extensiones PHP
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    git \
    unzip \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql bcmath

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Crear directorios que Nginx necesita
RUN mkdir -p /var/run /var/log/nginx \
    && chown -R root:root /var/run /var/log/nginx

# Definir directorio de trabajo
WORKDIR /var/www/html

# Copiar código de Laravel
COPY . .

# Instalar dependencias Laravel y dar permisos
RUN composer install --no-dev --optimize-autoloader \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 public

# Copiar configs de Nginx y Supervisor
COPY ./nginx.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisord.conf
COPY ./docker/fastcgi_params /var/www/html/docker/fastcgi_params

# Exponer puerto (solo informativo)
EXPOSE 8080

# Arrancar Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
