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

# Install dependencies (assumes filament is in composer.json, otherwise use composer require)
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Expose port 80
EXPOSE 80

# Run Laravel setup commands after container starts
CMD php artisan storage:link && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    apache2-foreground
