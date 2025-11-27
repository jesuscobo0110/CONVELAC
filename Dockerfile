FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip libpq-dev gettext \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY nginx.conf.template /etc/nginx/sites-available/default.template

WORKDIR /var/www/html
COPY . .

RUN composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 80

# CMD CORREGIDO (todo en una sola línea y con comillas bien cerradas)
CMD ["/bin/bash", "-c", "php artisan migrate --force --no-interaction && php artisan config:cache && php artisan route:cache && php artisan view:cache && envsubst '\\$PORT' < /etc/nginx/sites-available/default.template > /etc/nginx/sites-available/default && ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default && php-fpm -D && nginx -g 'daemon off;'"]