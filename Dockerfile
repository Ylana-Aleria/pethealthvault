FROM php:8.2-apache

# Install system dependencies and PHP extensions including PostgreSQL support
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

# Set working directory to Apache root
WORKDIR /var/www/html

# Copy application files into container
COPY . .
# Only copy if the file exists
COPY --chown=www-data:www-data .env.example .env

# Install PHP dependencies without dev and optimize autoloader
RUN composer install --no-dev --optimize-autoloader --no-scripts


# Install Filament 3 compatible with Laravel 11
RUN composer require filament/filament:"^3.0" --no-interaction --no-scripts

# Set ownership to www-data user for storage and cache folders
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 80 for Apache
EXPOSE 80

CMD php artisan config:clear && \
php artisan cache:clear && \
php artisan migrate --force && \
php artisan db:seed --force && \
php artisan storage:link && \
apache2-foreground

