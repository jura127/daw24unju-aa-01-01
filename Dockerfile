FROM php:8.2-apache

# 1. Instalar dependencias del sistema
RUN apt-get update && apt-get install -y git unzip

# 2. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Preparar el código
COPY . /var/www/html
WORKDIR /var/www/html

# 4. Instalar librerías de PHP (Esto crea la carpeta vendor)
RUN composer install --no-dev --optimize-autoloader

# 5. Permisos y puerto
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80
