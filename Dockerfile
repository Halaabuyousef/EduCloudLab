FROM webdevops/php-nginx:8.2

# تحديد مجلد العمل
WORKDIR /var/www/html

# نسخ كل الملفات
COPY . /var/www/html

# تثبيت المكتبات
RUN composer install --no-dev --optimize-autoloader

# متغيرات البيئة
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stack

# الكاش
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

EXPOSE 80

CMD ["supervisord"]
