#!/usr/bin/env bash
set -e
cd /home/forge/www.gayabmarketing.com
git pull origin develop/laravel-storefront
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
php artisan down || true
php artisan migrate --force
# Only the role/permission seeder runs on deploy — it's idempotent (Role::findOrCreate
# + givePermissionTo) and keeps access control in sync with code changes. The full
# `db:seed` also reseeds DatabaseSeeder's 4 demo catalog products via updateOrCreate,
# which would silently overwrite real admin edits (price, category, status) on every
# deploy — that seeder is for local/initial setup only, not production deploys.
php artisan db:seed --class="Database\Seeders\RolePermissionSeeder" --force
npm ci
npm run build
php artisan optimize
php artisan storage:link || true
php artisan queue:restart || true
php artisan up

