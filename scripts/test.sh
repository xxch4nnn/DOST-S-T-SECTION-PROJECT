#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> migrate test db"
php artisan migrate --force --database=testing --path=database/migrations

echo "==> phpunit"
php artisan test --no-interaction

echo "==> rollback"
php artisan migrate:rollback --force --database=testing --path=database/migrations || true
