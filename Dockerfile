<<<<<<< HEAD
#Docker Hub-eko PHP + Apache-ko irudi ofiziala

FROM php:8.2-apache

=======
>>>>>>> 4ae7a1dc115ddce3a8caad4e71b0a07738df8fb8
# 1. Instalar unzip y git (necesarios para composer)
RUN apt-get update && apt-get install -y \
    git \
    unzip
<<<<<<< HEAD

# 2. Instalar Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Copiar los archivos de tu proyecto al contenedor
COPY . /var/www/html

# 4. Establecer el directorio de trabajo
WORKDIR /var/www/html

# 5. EJECUTAR LA INSTALACIÓN DE DEPENDENCIAS (Esto es lo que te falta)
RUN composer install --no-dev --optimize-autoloader

# 6. Dar permisos a la carpeta de html
RUN chown -R www-data:www-data /var/www/html

=======
 
# 2. Instalar Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
 
# 3. Copiar los archivos de tu proyecto al contenedor
COPY . /var/www/html
 
# 4. Establecer el directorio de trabajo
WORKDIR /var/www/html
 
# 5. EJECUTAR LA INSTALACIÓN DE DEPENDENCIAS (Esto es lo que te falta)
RUN composer install --no-dev --optimize-autoloader
 
# 6. Dar permisos a la carpeta de html
RUN chown -R www-data:www-data /var/www/html
 
>>>>>>> 4ae7a1dc115ddce3a8caad4e71b0a07738df8fb8
# 7. Exponer el puerto 80
EXPOSE 80
