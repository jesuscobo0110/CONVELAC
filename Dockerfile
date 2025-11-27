# Dockerfile – versión final que funciona en Render Free (2025)
FROM richarvey/nginx-php-fpm:latest

# Instalamos todas las dependencias necesarias para Alpine Linux
RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    # Estas dos líneas son opcionales pero evitan problemas de versión en Alpine nuevo
    && echo "http://dl-cdn.alpinelinux.org/alpine/edge/main" >> /etc/apk/repositories \
    && echo "http://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories \
    && apk update

# Instalamos las extensiones PHP necesarias
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_pgsql

# Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiamos el código de la app
WORKDIR /var/www/html
COPY . .

# Instalamos dependencias de Composer (solo producción)
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Permisos correctos para Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configuración de Nginx específica para Laravel
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Puerto que escucha Render
EXPOSE 80

# Comando que se ejecuta al iniciar el contenedor
CMD php artisan key:generate --no-interaction --force && \
    php artisan migrate --force && \
    php artisan optimize:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php-fpm & nginx -g "daemon off;"