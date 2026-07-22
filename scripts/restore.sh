#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")/.."
if [ -z "${1:-}" ]; then echo "Usage: $0 <file.sql>"; exit 1; fi
docker compose exec -T mysql mysql -udostorage -psecret dostorage < "$1"
echo "Restored from: $1"
wc -l < "$1"
