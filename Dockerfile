FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions for PostgreSQL
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Install dependencies 
# Added --no-scripts to prevent "package:discover" from failing without an APP_KEY during build
RUN composer install --optimize-autoloader --no-dev --no-scripts

# FIX 1: Ensure directories exist before setting permissions
RUN mkdir -p /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/storage/framework/cache \
    /var/www/bootstrap/cache

# FIX 2: Set permissions so Laravel can write logs and cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Render uses port 10000 by default for many services, or detects EXPOSE
EXPOSE 10000

# FIX 3: Run discovery and migrations at RUNTIME, not build time
# We use "php artisan migrate --force" (not fresh) to keep your data safe.
CMD sh -c "php artisan package:discover --ansi && \
           php artisan config:cache && \
           php artisan route:cache && \
           php artisan view:cache && \
           php artisan migrate --force && \
           php artisan serve --host=0.0.0.0 --port=10000"
