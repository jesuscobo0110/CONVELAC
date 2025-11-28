FROM php:8.2-fpm

# --------------------------------------------------------
# 1. Instalar dependencias del sistema
# --------------------------------------------------------
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# --------------------------------------------------------
# 2. Extensiones PHP necesarias para Laravel
# --------------------------------------------------------
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# --------------------------------------------------------
# 3. Instalar Composer
# --------------------------------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --------------------------------------------------------
# 4. Configurar Nginx
# --------------------------------------------------------
RUN rm -f /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/sites-available/laravel
RUN ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/laravel

# --------------------------------------------------------
# 5. Copiar código de Laravel
# --------------------------------------------------------
WORKDIR /var/www/html
COPY . .

# --------------------------------------------------------
# 6. Instalar dependencias de Laravel (IMPORTANTE: sin || true)
# --------------------------------------------------------
RUN composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist --no-progress

# --------------------------------------------------------
# 7. Permisos correctos (775 para escritura)
# --------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# --------------------------------------------------------
# 8. Exponer el puerto 80 para nginx
# --------------------------------------------------------
EXPOSE 80

# --------------------------------------------------------
# 9. Comando final de arranque
# --------------------------------------------------------
CMD sh -c "php artisan migrate --force && php-fpm -D && nginx -g 'daemon off;'"
