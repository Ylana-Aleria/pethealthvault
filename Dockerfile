# Use official PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions (PostgreSQL, GD, etc)
RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev libsodium-dev libpq-dev unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) bcmath exif gd intl pdo_mysql sodium zip opcache pgsql pdo_pgsql \
    && a2enmod rewrite

# Set Apache to serve from Laravel's public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory to Laravel root
WORKDIR /var/www/html

# Copy Laravel app files
COPY . .

# Create storage directories and set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap \
    && chmod -R 775 storage bootstrap

# Switch to www-data user for safer install
USER www-data

# Install PHP dependencies including Filament
RUN composer require filament/filament && \
    composer install --no-dev --optimize-autoloader --no-scripts

# Switch back to root to fix permissions again
USER root
RUN chown -R www-data:www-data storage bootstrap && chmod -R 775 storage bootstrap

# Publish filament config and assets (needs to run as www-data)
USER www-data
RUN php artisan vendor:publish --tag=filament-config --force
RUN php artisan filament:install --force

# Switch back to root
USER root

# Expose Apache port
EXPOSE 80

# On container start: link storage, migrate, seed, cache config & routes, start Apache
CMD php artisan storage:link \
    && php artisan migrate --force \
    && php artisan db:seed --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && apache2-foreground
