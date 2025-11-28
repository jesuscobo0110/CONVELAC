FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear directorio de la app
WORKDIR /var/www/html

# Copiar código del proyecto
COPY . .

# Copiar configuración de Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Copiar configuración de supervisord
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Permisos de Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Instalar dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Exponer el puerto utilizado por Nginx
EXPOSE 80

# Iniciar supervisord (que inicia Nginx + PHP-FPM)
CMD ["/usr/bin/supervisord", "-n"]
