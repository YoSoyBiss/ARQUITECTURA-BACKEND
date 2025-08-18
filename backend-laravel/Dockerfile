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

# Crear directorios necesarios
RUN mkdir -p /var/run /var/log/nginx /var/www/html \
    && chown -R root:root /var/run /var/log/nginx

WORKDIR /var/www/html

# Copiar código de Laravel
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 public

# Copiar configs
COPY ./nginx.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisord.conf
COPY ./docker/fastcgi_params /var/www/html/docker/fastcgi_params

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
