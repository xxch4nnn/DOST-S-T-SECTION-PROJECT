#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")/.."
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p archive_local
docker compose exec -T mysql mysqldump -udostorage -psecret --no-tablespaces dostorage > "archive_local/backup_${DATE}.sql"
echo "Backup saved: archive_local/backup_${DATE}.sql"
wc -l < "archive_local/backup_${DATE}.sql"
