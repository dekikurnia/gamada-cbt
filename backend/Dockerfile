FROM php:8.3-fpm

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libicu-dev libxslt-dev zlib1g-dev libcurl4-openssl-dev pkg-config \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-install xml xsl dom intl opcache curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory di container
WORKDIR /var/www/html

# Copy semua source Laravel
COPY src/ ./

# Install dependencies Laravel
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Set permission folder storage & bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
