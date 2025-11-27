FROM richarvey/nginx-php-fpm:latest

RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
&& docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --optimize-autoloader --no-dev --no-scripts

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html/storage

COPY ./nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD php artisan key:generate --no-interaction && \
    php artisan migrate --force && \
    php artisan optimize && \
    php-fpm & nginx -g "daemon off;"