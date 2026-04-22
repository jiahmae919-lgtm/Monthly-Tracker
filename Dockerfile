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

# FIX: We set a temporary APP_KEY and DB_CONNECTION just for the build process
# This prevents artisan discovery from failing when it can't find these values.
ENV APP_KEY=base64:nz9T9vH/S2S8vX6p5p6p5p6p5p6p5p6p5p6p5p6p5p6=
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=:memory:

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

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

# The actual start command
# At this stage, Render will override our "Fake" ENV vars with your real Dashboard secrets.
CMD sh -c "php artisan config:clear && \
           php artisan package:discover --ansi && \
           php artisan config:cache && \
           php artisan route:cache && \
           php artisan view:cache && \
           php artisan migrate --force && \
           php artisan serve --host=0.0.0.0 --port=10000"
