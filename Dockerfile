# Use the official PHP image
FROM php:8.2-apache

# Copy all files to the web directory
COPY . /var/www/html/

# Enable mysqli extension (for MySQL)
RUN docker-php-ext-install mysqli

# Expose port 80 for Render
EXPOSE 80
