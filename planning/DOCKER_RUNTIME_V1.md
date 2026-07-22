# DOSTorage V1 — Docker Runtime V1
Verification Date: 2026-07-22

## Verified
- [x] compose.yaml replaced with nginx + php-fpm + MySQL runtime
- [x] Dockerfile updated and image builds
- [x] app accessible at http://localhost:8000
- [x] migrate:fresh --force passes inside container
- [x] Data survives compose down / up

## Verification Outputs

### `docker compose ps` (after final up)
```
NAME                IMAGE           COMMAND                  SERVICE   CREATED          STATUS                    PORTS
dostorage-app-1     dostorage-app   ...                      app       ...              Up ...                    0.0.0.0:8000->80/tcp
dostorage-mysql-1   mysql:8.4       ...                      mysql     ...              Up ... (healthy)          0.0.0.0:3307->3306/tcp
```
Note: only `mysql` defines a healthcheck; `app` shows `Up` (HTTP 200). Host MySQL port mapped to **3307** because **3306** was already bound (local WAMP).

### HTTP
- `GET http://localhost:8000/` → **200**
- `GET http://localhost:8000/health` → `{"status":"ok",...}` **200**

### Migrations
`docker compose exec app php artisan migrate:fresh --force` → exit 0  
Tables created include: `users`, `scholars`, `documents`, Spatie permission tables, etc. (26 tables in `dostorage`).

### Volume persistence
| Step | Result |
|------|--------|
| A (before) | `user_count = 1` (`persist@test.local` inserted) |
| B | `docker compose down` |
| C | `docker compose up -d` |
| D (after) | `user_count = 1`, email `persist@test.local` still present |

**Pass:** counts match; named volume `dostorage_sail-mysql` retained data.

## Local env (not committed)
`.env` updated for Docker MySQL (`DB_CONNECTION=mysql`, `DB_HOST=mysql`, …) plus:
```
APP_PORT=8000
WWWUSER=1000
FORWARD_DB_PORT=3307
```
`.env` remains gitignored (do not commit secrets).

## Intentional deviations from handoff draft
1. **Removed** `sail-mysql` bind from the `app` service (MySQL data belongs only on `mysql`).
2. Set `DB_CONNECTION=mysql` in compose `environment` so the app does not keep SQLite when `.env` is wrong.
3. Dockerfile package names for Debian `php:8.3-fpm`: `libonig-dev`, `default-mysql-client` (not Alpine `oniguruma-dev` / `mysql-client`).
4. Nginx config path: `/etc/nginx/conf.d/default.conf` (+ remove `sites-enabled/default`), not Alpine `http.d`.
5. Create `storage` / `bootstrap/cache` before `chown` at build time.
6. Docker Hub DNS: added `"dns": ["8.8.8.8","1.1.1.1"]` to `%USERPROFILE%\.docker\daemon.json` so pulls succeed on this machine.

## How to run again
```bash
docker compose build
docker compose up -d
docker compose exec app php artisan migrate:fresh --force
# open http://localhost:8000
```
