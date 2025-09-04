FROM webdevops/php-nginx:8.2-alpine

WORKDIR /var/www/html

# System deps
RUN apk --no-cache add \
    libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev \
    libxml2-dev postgresql-dev git curl zip unzip

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Project files
COPY . .

# Install Laravel deps
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Nginx vhost
RUN mkdir -p /opt/docker/etc/nginx
COPY conf/nginx/nginx-site.c
