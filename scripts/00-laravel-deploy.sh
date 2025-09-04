#!/usr/bin/env bash

echo "🚀 Running composer install..."
composer install --no-dev --optimize-autoloader

echo "⚙️ Caching config..."
php artisan config:cache

echo "⚙️ Caching routes..."
php artisan route:cache

echo "⚙️ Migrating database..."
php artisan migrate --force
