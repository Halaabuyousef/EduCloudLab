# صورة أساسية فيها PHP + Apache
FROM php:8.2-apache

# تثبيت الإضافات المطلوبة للـ Laravel
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# تفعيل mod_rewrite (مطلوب للـ Laravel routes)
RUN a2enmod rewrite

# نسخ المشروع إلى مجلد السيرفر
COPY . /var/www/html

# اجعل Apache يوجّه إلى public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# صلاحيات الملفات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# أمر التشغيل
CMD ["apache2-foreground"]
