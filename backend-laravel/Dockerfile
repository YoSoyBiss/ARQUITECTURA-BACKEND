FROM php:8.2-fpm-alpine

# Instalar dependencias necesarias
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    git \
    unzip \
    autoconf \
    g++ \
    make \
    pkgconf \
    re2c \
    && docker-php-ext-install pdo pdo_mysql bcmath

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Copiar el código de la aplicación de Laravel
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Configurar permisos para la aplicación de Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copiar archivos de configuración
COPY ./nginx.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisord.conf
COPY ./docker/fastcgi_params /etc/nginx/fastcgi_params

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]