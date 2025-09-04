# صورة فيها PHP 8.2 + Nginx جاهزين
FROM webdevops/php-nginx:8.2-alpine

# إعداد مجلد العمل
WORKDIR /var/www/html

# انسخ المشروع
COPY . .

# تأكد من وجود مجلد إعدادات Nginx ثم انسخ ملفك
RUN mkdir -p /opt/docker/etc/nginx
COPY conf/nginx/nginx-site.conf /opt/docker/etc/nginx/vhost.conf

# ثبّت مكتبات النظام اللي يحتاجها Laravel
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

# ثبّت امتدادات PHP (بعد ما نزلنا المكتبات)
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

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# سكربت النشر
COPY scripts/00-laravel-deploy.sh /usr/local/bin/laravel-deploy.sh
RUN chmod +x /usr/local/bin/laravel-deploy.sh

# Laravel لازم يستخدم public
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# البورت
EXPOSE 80
