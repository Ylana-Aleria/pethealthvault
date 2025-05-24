# Use official PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev libsodium-dev libpq-dev unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) bcmath exif gd intl pdo_mysql sodium zip opcache pgsql pdo_pgsql \
    && a2enmod rewrite

# Set Apache to serve from Laravel's public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel files
COPY . .

# Set correct permissions before Composer actions
RUN chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html

# Run as root to install Filament and dependencies
RUN composer require filament/filament:^3.2 --no-scripts && \
    composer install --no-dev --optimize-autoloader --no-scripts

# Set proper permissions for storage and bootstrap
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap && \
    chmod -R 775 storage bootstrap

# Switch to www-data to run artisan safely
USER www-data

# Run artisan commands for Filament
RUN php artisan vendor:publish --tag=filament-config --force && \
    php artisan filament:install --force

# Switch back to root to expose port and launch apache
USER root

# Expose Apache port
EXPOSE 80

# Final CMD to run app
CMD php artisan storage:link && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    apache2-foreground
