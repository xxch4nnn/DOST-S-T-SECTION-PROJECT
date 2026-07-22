# DOSTorage V1 — Spatie Roles / Permissions Baseline
Verification Date: 2026-07-22

## Scope
Baseline for checklist items:
- Seed roles: Super Admin, Admin, Encoder
- Seed V1 permission matrix and attach to roles
- Encoder lacks `manageUsers`; Super Admin has all

## Roles
| Role | Permissions |
|------|-------------|
| Super Admin | viewAuditLogs, manageUsers, uploadDocuments, editDocumentMetadata, strikeOffDocuments, viewReports |
| Admin | same as Super Admin (V1) |
| Encoder | uploadDocuments, editDocumentMetadata |

## Seeded user
- `test@example.com` → Super Admin (from `DatabaseSeeder`)

## Verification
```bash
docker compose exec app php artisan migrate:fresh --force --seed
docker compose exec app php artisan test --filter=RolesAndPermissionsBaselineTest
```

## Result
- Feature test: `Tests\Feature\RolesAndPermissionsBaselineTest` — **5 passed**
- Full suite after change: **33 passed**
- Docker seed: `RolesAndPermissionsSeeder` completes; `test@example.com` assigned Super Admin
