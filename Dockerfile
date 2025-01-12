# use official PHP image
FROM php:8.2-apache

# Install extensions for PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# copy source code into container
COPY / /var/www/html/

# make sure right permision for Apache
RUN chown -R www-data:www-data /var/www/html

# Turn on mod_rewrite (if needed)
RUN a2enmod rewrite

# use this to run header
RUN a2enmod headers
RUN service apache2 restart