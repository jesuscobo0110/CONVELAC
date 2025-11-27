FROM php:8.2-fpm

# Dependencias del sistema (Debian, no Alpine para evitar bugs)
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar e instalar extensiones PHP (incluyendo pdo_pgsql con ruta correcta)
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Nginx para Laravel
COPY nginx.conf.template /etc/nginx/sites-available/default.template
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/ \
    && rm -f /etc/nginx/sites-enabled/default

# Copiar código de la app
WORKDIR /var/www/html
COPY . .

# Instalar dependencias Laravel (producción)
RUN composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 80

# Comando de inicio (migrations, caches, PHP-FPM + Nginx)
CMD php artisan migrate --force && \
    php artisan optimize:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    envsubst '${PORT}' < /etc/nginx/sites-available/default.template > /etc/nginx/sites-available/default && \
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/ && \
    php-fpm -F & nginx -g "daemon off;"