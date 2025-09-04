FROM php:8.2-apache

# تثبيت إضافات PHP: pdo_pgsql (لـ PostgreSQL)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# تمكين mod_rewrite (مطلوب للـ Laravel routes)
RUN a2enmod rewrite

# تعيين مجلد العمل
WORKDIR /var/www/html

# نسخ المشروع داخل الحاوية
COPY . /var/www/html

# إضافة ملف إعداد Apache مخصص يوجه للـ public/
COPY conf/laravel-apache.conf /etc/apache2/sites-available/laravel.conf
RUN a2dissite 000-default && a2ensite laravel

# صلاحيات مجلدات Laravel التي تحتاج كتابة
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
