FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libpng-dev \
    libonig-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        mbstring \
        intl \
        xml \
        gd \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy Laravel application
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install production dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# Laravel storage permissions
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Laravel public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Configure Apache document root
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Render HTTP port
RUN sed -ri 's/^Listen 80$/Listen 10000/' \
    /etc/apache2/ports.conf && \
    sed -ri 's/:80>/:10000>/g' \
    /etc/apache2/sites-available/*.conf

EXPOSE 10000

CMD ["apache2-foreground"]