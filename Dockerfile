FROM php:8.2-apache

# ثبّت المكتبات المطلوبة
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip unzip git curl \
    && docker-php-ext-install pdo pdo_pgsql

# فعّل mod_rewrite
RUN a2enmod rewrite

# عدل الـ VirtualHost عشان يشير إلى public/
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# انسخ المشروع
WORKDIR /var/www/html
COPY . .

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# كاش Laravel
RUN php artisan config:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache

# صلاحيات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 10000
CMD ["apache2-foreground"]
