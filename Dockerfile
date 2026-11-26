FROM php:8.2-apache

# Instalar extensiones PDO para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar módulos necesarios de Apache
RUN a2enmod rewrite headers

# Configurar Apache para escuchar en el puerto 8080 (estándar de Google Cloud Run)
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Permitir reglas de seguridad en .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080
