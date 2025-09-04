# صورة فيها PHP 8.2 + Nginx جاهزين
FROM webdevops/php-nginx:8.2-alpine

# إعداد مجلد العمل
WORKDIR /var/www/html

# انسخ المشروع
COPY . .

# انسخ إعدادات Nginx من مشروعك لمسار vhost.conf
COPY conf/nginx/nginx-site.conf /opt/docker/etc/nginx/vhost.conf

# ثبّت المكتبات اللي يحتاجها Laravel
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# ثبّت Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# نزّل باكدجات Laravel
RUN composer install --no-dev --optimize-autoloader

# نسخ سكربت النشر
COPY scripts/00-laravel-deploy.sh /usr/local/bin/laravel-deploy.sh
RUN chmod +x /usr/local/bin/laravel-deploy.sh

# Laravel لازم يستخدم public
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# البورت
EXPOSE 80
