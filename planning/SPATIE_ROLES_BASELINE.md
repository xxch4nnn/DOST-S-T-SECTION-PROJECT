# DOSTorage V1 — Spatie Roles / Permissions Baseline
Verification Date: 2026-08-04 (route gates + expanded matrix)

## Scope
- Seed roles: Super Admin, Admin, Encoder
- Seed V1 permission matrix and attach to roles
- Route-level Spatie middleware in `routes/web.php`
- Policies registered via `AuthServiceProvider` (+ Super Admin `Gate::before`)
- Encoder lacks `manageUsers`, `strikeOffDocuments`, `viewAuditLogs`, admin create/edit/delete, scholar delete

## Roles
| Role | Permissions |
|------|-------------|
| Super Admin | all (also Gate::before bypass) |
| Admin | all seeded permissions |
| Encoder | uploadDocuments, editDocumentMetadata, viewReports, viewScholars, createScholars, editScholars, viewAdminRecords |

## Route gates (summary)
| Area | Middleware |
|------|------------|
| dashboard | `role:Super Admin\|Admin\|Encoder` |
| scholars index/show | `permission:viewScholars` |
| scholars edit | `permission:editScholars` |
| add-file | `permission:uploadDocuments` |
| admin-records index/show | `permission:viewAdminRecords` |
| admin-records create | `permission:createAdminRecords` |
| admin-records edit | `permission:editAdminRecords` |
| audit-logs | `permission:viewAuditLogs` |
| documents download | `DocumentPolicy::download` (403 if denied) |

## Seeded user
- `test@example.com` → Super Admin (from `DatabaseSeeder`)

## Verification
```bash
php artisan test --filter=RolesAndPermissionsBaselineTest
php artisan test --filter=RoutePermissionGateTest
php artisan permission:cache-clear
```

## Note
FS-07 policies were merged to `feat/fe-08-fullstack-hardening` (#24) but were **not** on `master` until this stitch slice.
