# DOSTorage V1 — Docker Runtime V1
Verification Date: 2026-07-22

## Stack summary
- **Chosen:** Laravel 13 / Livewire 4 / Spatie / Bootstrap 5 / MySQL 8.4
- **Decision:** Keep Laravel 13. Do **not** downgrade Livewire (remain on `^4.3.3`).
- Runtime: nginx + php-fpm 8.3 in `dostorage-app`, MySQL 8.4 with named volume `sail-mysql`, private uploads volume `storage_private`.

## Verified
- [x] compose.yaml replaced with nginx + php-fpm + MySQL runtime
- [x] Dockerfile updated and image builds
- [x] app accessible at http://localhost:8000 (HTTP 200)
- [x] migrate:fresh --force passes inside container
- [x] Data survives compose down / up (A == D)
- [x] Test suite green: **28 passed** (Hour 74 backtrack)

## Volume persistence (rechecked Hour 74)
| Step | Result |
|------|--------|
| A | `COUNT(*)` users = 0 |
| B | `docker compose down` |
| C | `docker compose up -d` |
| D | `COUNT(*)` users = 0 |
| E | `curl http://localhost:8000` → **200** |

**Pass:** A == D and E == 200. Host MySQL published on **3307** (`FORWARD_DB_PORT`) because 3306 is occupied by local WAMP.

## Known test failures
None remaining after Hour 74 fixes:
- Widened `file_types.year` to `varchar(50)`
- Capture upload metadata **before** `storeAs` (Livewire temp file lifecycle)
- Force `APP_ENV=testing` via `tests/bootstrap.php` so Livewire Volt `assertSeeVolt` macros register under Docker

## Local env (not committed)
`.env` uses MySQL (`DB_HOST=mysql`) plus `APP_PORT=8000`, `WWWUSER=1000`, `FORWARD_DB_PORT=3307`. Remains gitignored.

## How to run again
```bash
docker compose build
docker compose up -d
docker compose exec app php artisan migrate:fresh --force --seed
docker compose exec app php artisan test
# open http://localhost:8000
```
