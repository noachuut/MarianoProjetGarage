# Apache + PHP image for the Garage application
FROM php:8.2-apache

# Install PHP extensions required for PDO MySQL connections
RUN docker-php-ext-install pdo pdo_mysql

# Copy application source code
WORKDIR /var/www/html
COPY . /var/www/html

# Ensure Apache can read the files
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
