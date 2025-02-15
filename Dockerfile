# Use the official Ubuntu 20.04 image as the base image
FROM ubuntu:20.04

# Set environment variables to non-interactive (this avoids prompting for input during package installation)
ENV DEBIAN_FRONTEND=noninteractive

# Update package list and install required dependencies for PHP and other utilities
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    gcc \
    make \
    autoconf \
    pkg-config \
    libxml2-dev \
    libzip-dev \
    software-properties-common \
    && apt-get clean

# Install PHP and necessary extensions
RUN add-apt-repository ppa:ondrej/php && apt-get update && apt-get install -y \
    php8.1-cli \
    php8.1-gd \
    php8.1-xml \
    php8.1-mbstring \
    php8.1-curl \
    php8.1-zip \
    php8.1-json \
    && apt-get clean

# Install Composer (PHP dependency manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the current directory contents into the container
COPY . .

# Expose port 10000 (or another port if necessary)
EXPOSE 10000

# Start the PHP built-in server
CMD ["php", "-S", "0.0.0.0:10000", "index.php"]
