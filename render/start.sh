#!/usr/bin/env sh
set -e

touch /var/www/database/database.sqlite
php artisan migrate --force --seed

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan serve --host 0.0.0.0 --port "${PORT:-8000}"
