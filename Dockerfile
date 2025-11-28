# ---------------------------------------------------------
# Imagen base: PHP 8.2 con FPM
# ---------------------------------------------------------
FROM php:8.2-fpm

# ---------------------------------------------------------
# Instalar dependencias del sistema
# ---------------------------------------------------------
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    postgresql-server-dev-all \
    nodejs \
    npm \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# Extensiones PHP necesarias para Laravel
# ---------------------------------------------------------
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
 && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# ---------------------------------------------------------
# Instalar Composer
# ---------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------------------------------------------------------
# Configurar Nginx
# ---------------------------------------------------------
COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default

# ---------------------------------------------------------
# Configurar Supervisor
# ---------------------------------------------------------
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ---------------------------------------------------------
# Copiar proyecto
# ---------------------------------------------------------
WORKDIR /var/www/html
COPY . .

# ---------------------------------------------------------
# Instalar dependencias de Laravel
# ---------------------------------------------------------
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# ---------------------------------------------------------
# Build de frontend (si usas Vite)
# ---------------------------------------------------------
RUN npm install && npm run build

# ---------------------------------------------------------
# Permisos
# ---------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ---------------------------------------------------------
# Puerto expuesto
# ---------------------------------------------------------
EXPOSE 80

# ---------------------------------------------------------
# Ejecutar Supervisor
# ---------------------------------------------------------
CMD ["/usr/bin/supervisord", "-n"]
