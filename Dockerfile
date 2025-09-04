FROM webdevops/php-nginx:8.2

WORKDIR /app

COPY . /app

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 80
