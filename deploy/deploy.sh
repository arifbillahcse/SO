#!/usr/bin/env bash
#
# Softorio — deployment script
#
# Run this FROM the server, in the project root (e.g. /var/www/softorio),
# after SSH'ing in. Not meant to run inside this sandboxed session —
# it needs the real production vendor/, database, and services.
#
# Usage: ./deploy/deploy.sh

set -euo pipefail

echo "==> Entering maintenance mode"
php artisan down --retry=60 || true

echo "==> Pulling latest code"
git pull origin main

echo "==> Installing PHP dependencies (production, no dev packages)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Installing and building frontend assets"
npm ci
npm run build

echo "==> Running database migrations"
php artisan migrate --force

echo "==> Clearing and rebuilding caches"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Restarting queue workers (picks up new code)"
php artisan queue:restart

echo "==> Restarting PHP-FPM"
sudo systemctl reload php8.3-fpm

echo "==> Leaving maintenance mode"
php artisan up

echo "==> Deploy complete"
