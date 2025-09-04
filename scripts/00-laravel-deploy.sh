#!/bin/sh
set -e

echo "🚀 Running Laravel deploy script..."

# تأكد إنك داخل مجلد المشروع
cd /var/www/html

# أعطِ صلاحيات للمجلدات اللي Laravel يحتاجها
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# نسخ/تجهيز .env من متغيرات Render
if [ ! -f .env ]; then
  echo "APP_KEY=$APP_KEY" > .env
  echo "APP_ENV=$APP_ENV" >> .env
  echo "APP_DEBUG=$APP_DEBUG" >> .env
  echo "APP_URL=$APP_URL" >> .env
  echo "DB_CONNECTION=$DB_CONNECTION" >> .env
  echo "DATABASE_URL=$DATABASE_URL" >> .env
fi

# تشغيل أوامر Laravel المعتادة للنشر
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache

php artisan view:cache

# تشغيل migrations في البيئة الحقيقية
php artisan migrate --force

echo "✅ Laravel deploy script finished!"
