FROM webdevops/php-nginx:8.2-alpine

WORKDIR /var/www/html

# انسخ المشروع
COPY . .

# تأكد من وجود مجلد إعدادات Nginx ثم انسخ ملفك
RUN mkdir -p /opt/docker/etc/nginx
COPY conf/nginx/nginx-site.conf /opt/docker/etc/nginx/vhost.conf

# ثبّت مكتبات النظام اللي Laravel يحتاجها
RUN apk --no-cache add \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    git \
    curl \
    zip \
    unzip

# ثبّت امتدادات PHP
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# نزّل باكدجات Laravel
RUN composer install --no-dev --optimize-autoloader

# سكربت النشر
COPY scripts/00-laravel-deploy.sh /usr/local/bin/laravel-deploy.sh
RUN chmod +x /usr/local/bin/laravel-deploy.sh

ENV WEB_DOCUMENT_ROOT=/var/www/html/public

EXPOSE 80
