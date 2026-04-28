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

# FIX: Set dummy variables for the build phase
ENV APP_KEY=base64:nz9T9vH/S2S8vX6p5p6p5p6p5p6p5p6p5p6p5p6p5p6=
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=:memory:

# THE FIX: Added --no-scripts to ensure composer doesn't try to run Artisan
# before the container is actually running on Render.
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Ensure directories exist
RUN mkdir -p /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/storage/framework/cache \
    /var/www/bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Render Port
EXPOSE 10000

# The Startup Command
# We run discovery and clear config here, where Render's REAL variables are present.
CMD sh -c "php artisan package:discover --ansi && \
           php artisan config:clear && \
           php artisan config:cache && \
           php artisan route:cache && \
           php artisan view:cache && \
           php artisan migrate --force && \
           php artisan schedule:work > /var/www/storage/logs/scheduler.log 2>&1 & \
           php artisan serve --host=0.0.0.0 --port=10000"
