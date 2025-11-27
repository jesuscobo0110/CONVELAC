FROM php:8.2-fpm

# Dependencias del sistema
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
    gettext \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql mbstring exif pcntl bcmath gd zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Template de Nginx
COPY nginx.conf.template /etc/nginx/sites-available/default.template

# Código de la app
WORKDIR /var/www/html
COPY . .

# Dependencias Laravel
RUN composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 80

# COMANDO FINAL (todo en una sola línea con bash -c para que funcione en Render)
CMD /bin/bash -c " \
    php artisan migrate --force --no-interaction && \
    php artisan optimize:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    envsubst '${PORT}' < /etc/nginx/sites-available/default.template > /etc/nginx/sites-available/default && \
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default && \
    php-fpm -D && \
    nginx -g 'daemon off;' \
"