# ============================
# Imagen base PHP 8.3 + FPM
# ============================
FROM php:8.3-fpm

# ----------------------------
# Instalar dependencias del sistema
# ----------------------------
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
    nodejs \
    npm \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# ----------------------------
# Extensiones de PHP necesarias para Laravel
# ----------------------------
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# ----------------------------
# Instalar Composer
# ----------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ----------------------------
# Copiar código de la aplicación
# ----------------------------
WORKDIR /var/www/html
COPY . .

# ----------------------------
# Instalar dependencias de Laravel
# ----------------------------
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# ----------------------------
# Construir assets (Vite)
# ----------------------------
RUN npm ci --no-audit --prefer-offline
RUN npm run build

# ----------------------------
# Permisos correctos
# ----------------------------
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ----------------------------
# Copiar configuraciones de nginx y supervisord
# ----------------------------
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ----------------------------
# Exponer puerto 8080 (Render requiere NO usar 80)
# ----------------------------
EXPOSE 8080

# ----------------------------
# Comando por defecto: supervisord
# ----------------------------
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
