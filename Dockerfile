FROM php:8.2-apache

# تثبيت الإضافات المطلوبة للـ Laravel
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# تثبيت Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# نسخ المشروع
COPY . /var/www/html

WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# تفعيل mod_rewrite
RUN a2enmod rewrite

# تعديل DocumentRoot ليشير إلى public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# إضافة إعدادات Laravel (للسماح بالـ .htaccess)
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
 && a2enconf laravel

# صلاحيات
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

CMD ["apache2-foreground"]
