# Use an official PHP image as the base image
FROM php:8.2-cli

# Set the working directory inside the container
WORKDIR /app

# Copy the PHP script into the container
COPY . /app

# Install necessary dependencies (fsockopen is already enabled in PHP CLI)
# If you need additional extensions, install them here
RUN docker-php-ext-install sockets

# Expose port 80 (or any other port you want to use)
EXPOSE 80

# Command to run the PHP script
CMD ["php", "-S", "0.0.0.0:80"]
