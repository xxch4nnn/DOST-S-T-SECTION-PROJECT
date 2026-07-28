# HANDOFF_RUNBOOK.md

## Prerequisites

- PHP 8.3
- Composer
- Node 22
- MySQL 8.4
- Git
- npm

## First-time setup

1. Clone the repo: `git clone https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT.git`
2. Install dependencies: `composer install` and `npm install`
3. Copy `.env.example` to `.env`
4. Generate app key: `php artisan key:generate --force`
5. Set database credentials in `.env`
6. Run migrations: `php artisan migrate --force`
7. Build assets: `npm run build`

For local dev without Docker, also run: `php artisan serve --host=0.0.0.0 --port=8000` and `npm run dev`.

## Running tests

Run the full test suite:
```bash
php artisan test
```

Run only authentication tests:
```bash
php artisan test --filter=AuthenticationTest
```

## Running dev server

```bash
composer dev
```

Or run server + Vite separately:
```bash
php artisan serve --host=0.0.0.0 --port=8000
npm run dev
```

## Backup/restore

Backup:
```bash
./scripts/backup.sh
```
Output: `archive_local/backup_<timestamp>.sql`

Restore:
```bash
./scripts/restore.sh archive_local/backup_<timestamp>.sql
```

Requires Docker runtime with `mysql` service running.

## Known issues / troubleshooting

- If `npm run lint:css` fails with stylelint errors, run `npm run build` first or check `.stylelintrc.json` for ignored font tokens.
- If tests fail on MySQL locally, ensure the database exists and credentials in `.env` match.
- Docker `HEALTHCHECK` is configured in `Dockerfile`; container may take ~10s to report healthy.
- `php artisan test --no-interaction` is not supported by the project's PHPUnit setup; omit the flag.
