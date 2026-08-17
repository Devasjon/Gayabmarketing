#!/usr/bin/env bash
set -e
cd /home/forge/www.gayabmarketing.com
git pull origin develop/laravel-storefront
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
php artisan down || true
php artisan migrate --force
php artisan db:seed --force
npm ci
npm run build
php artisan optimize
php artisan storage:link || true
php artisan queue:restart || true
php artisan up

