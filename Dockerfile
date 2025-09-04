# استخدم PHP 8.2 مع FPM
FROM php:8.2-fpm

# ثبّت أدوات النظام وامتدادات PHP اللي Laravel يحتاجها
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# ثبّت Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# أنشئ مجلد العمل
WORKDIR /var/www/html

# انسخ ملفات المشروع
COPY . .

# انسخ إعدادات Nginx (لازم يكون عندك nginx.conf جاهز بمجلد conf/nginx)
COPY conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# ثبّت الحزم
RUN composer install --no-dev --optimize-autoloader

# نسخ سكربت النشر
COPY scripts/00-laravel-deploy.sh /usr/local/bin/laravel-deploy.sh
RUN chmod +x /usr/local/bin/laravel-deploy.sh

# شغل Nginx + PHP-FPM
CMD service nginx start && php-fpm
