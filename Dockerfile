# 1. Usamos la imagen de PHP con Apache
FROM php:8.2-apache
 
# 2. Instalamos dependencias del sistema y herramientas necesarias (git y zip)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip
 
# 3. Instalamos Composer dentro del contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
 
# 4. Copiamos los archivos de nuestra app al contenedor
COPY . /var/www/html
 
# 5. Ejecutamos composer install para crear la carpeta 'vendor'
# Usamos --no-dev para que sea más ligero en producción
RUN composer install --no-interaction --optimize-autoloader --no-dev
 
# 6. Permisos necesarios para que Apache pueda leer los archivos
RUN chown -R www-data:www-data /var/www/html
 
EXPOSE 80
