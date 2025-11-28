FROM php:8.2-fpm

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    git curl zip unzip nginx supervisor \
    && apt-get clean

# Extensiones PHP necesarias para Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Copiar código Laravel
WORKDIR /var/www/html
COPY . .

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Copiar configuraciones
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Dar permisos
RUN chown -R www-data:www-data /var/www/html

# Puerto que Render detecta
EXPOSE 8080

# Comando de inicio
CMD ["/usr/bin/supervisord"]
