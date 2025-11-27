FROM php:8.2-fpm

# Instalamos nginx y dependencias
RUN apt-get update && apt-get install -y nginx libpq-dev && rm -rf /var/lib/apt/lists/*

# Extensiones PHP
RUN docker-php-ext-install pdo pdo pdo_pgsql pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Eliminamos el config por defecto de nginx
RUN rm -f /etc/nginx/sites-enabled/default

# Copiamos nuestro config directamente al lugar correcto
COPY nginx.conf /etc/nginx/sites-available/laravel
RUN ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/

# Código de la app
WORKDIR /var/www/html
COPY . .

# Composer
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php-fpm -D && \
    nginx -g 'daemon off;'