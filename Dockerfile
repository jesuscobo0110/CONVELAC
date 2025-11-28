FROM php:8.2-fpm

# -------- SISTEMA --------
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# -------- EXTENSIONES PHP --------
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# -------- INSTALAR NODE 18 --------
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# -------- COMPOSER --------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -------- ARCHIVOS APP --------
WORKDIR /var/www/html
COPY . .

# -------- DEPENDENCIAS PHP --------
RUN composer install --no-dev --optimize-autoloader --no-interaction

# -------- BUILD FRONT-END --------
RUN npm ci --no-audit --prefer-offline
RUN npm run build

# -------- PERMISOS --------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# -------- NGINX --------
RUN rm -f /etc/nginx/sites-enabled/default
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# -------- SUPERVISOR --------
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
