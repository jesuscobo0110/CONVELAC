# Usa la imagen oficial de PHP 8.3 con FPM
FROM php:8.3-fpm

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    curl nodejs npm && \
    docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear carpeta del proyecto
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

#  **IMPORTANTE: construir Vite**
RUN npm install
RUN npm run build

# Dar permisos a storage y cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Exponer puerto usado por PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
