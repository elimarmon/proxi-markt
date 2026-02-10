FROM php:8.2-fpm-alpine

# Instalar extensiones necesarias para MySQL y carpetas
RUN docker-php-ext-install pdo pdo_mysql

# Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiamos el código
COPY . .

# Instalamos dependencias y ajustamos permisos
RUN composer install --no-dev --optimize-autoloader && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponemos el puerto para PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]