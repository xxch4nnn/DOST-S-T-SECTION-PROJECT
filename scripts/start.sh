#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> composer install"
composer install --no-interaction --prefer-dist --no-progress

echo "==> env setup"
cp -n .env.example .env || true
php artisan key:generate --force

echo "==> migrations + seeders"
php artisan migrate --force --seed

echo "==> test suite"
./scripts/test.sh

echo "==> local server hint"
echo "php artisan serve --host=0.0.0.0 --port=8000"
