#!/usr/bin/env bash
set -e

echo "👉 Installing PHP dependencies (Composer)..."
composer install --no-dev --optimize-autoloader

echo "👉 Running migrations..."
php artisan migrate --force || echo "⚠️ Migration step failed or already up to date."

echo "👉 Clearing caches..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "👉 Caching config & routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "✅ Laravel deployment script finished."
