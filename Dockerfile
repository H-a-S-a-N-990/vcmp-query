# Use an official PHP-Apache image
FROM php:8.1-apache

# Enable required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy project files to the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 80 for web traffic
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
