FROM php:8.2-apache

# Install system dependencies and PHP extensions (including PostgreSQL support)
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libsodium-dev \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        intl \
        pdo_mysql \
        sodium \
        zip \
        opcache \
        pgsql \
        pdo_pgsql \
    && a2enmod rewrite

# Set Apache to serve from Laravel's public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all Laravel project files
COPY . .

# Create required storage and cache directories, set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap && \
    chmod -R 775 storage bootstrap

# Switch to www-data to safely run Composer and Artisan
USER www-data

# Install Composer dependencies (skip scripts initially)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Run artisan package discovery
RUN php artisan package:discover --ansi

# Switch back to root
USER root

# Re-apply permissions just in case
RUN chown -R www-data:www-data storage bootstrap && chmod -R 775 storage bootstrap

# Expose port 80
EXPOSE 80

# Run Laravel setup commands at container start
CMD php artisan storage:link && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    apache2-foreground
