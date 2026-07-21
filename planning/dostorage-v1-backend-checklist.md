# DOSTorage V1 — Exhaustive Backend / DB Feature Checklist

## Purpose
Implementation contract for Wakin (Backend & DB + QA). Every item below is backend-facing: entities, tables, fields, indexes, relationships, migrations, validation, actions, permissions, and lifecycle behavior.

## Scope rules
- V1 = Scholar 201 + Administrative Records only.
- Financial Ledger is explicitly future scope.
- Offline-first: local Docker runtime is mandatory; do not design around cloud dependency.
- Deletion is never hard-delete in V1. Use soft-delete / strike-off / archive lifecycle only.

---

## 1. Users, Roles, and Permissions

### Tables
- `users`
- `model_has_roles` / `roles` / `permissions` / `role_has_permissions`

### Fields/DB requirements
- `users.id` unsigned bigint PK
- `users.email` unique, indexed
- `users.password` hashed
- `users.name` required
- Spatie role assignment must be enforced at the DB level via middleware/policy, not just UI hiding

### Roles
- Super Admin
- Admin
- Encoder

### Backend concerns
- Seed roles/permissions during migration/seed stage
- Gate permission checks on all document mutation routes
- Role audit trail in `audit_logs`

---

## 2. Scholar 201 Records

### Table: `scholars`

| Field | Type | Notes |
|-------|------|-------|
| id | unsigned bigint PK, auto-increment | Business identifier |
| spas_no | string, indexed, nullable | Not unique; pre-2000 unreliable |
| document_type | string, indexed | |
| date_issued | date, indexed | |
| full_name | string, required | |
| ... | ... | Add complete scholar profile fields |

### Indexes
- Composite index on `spas_no + document_type + date_issued` for lookup
- Fulltext or separate index fields for global search

### Validation
- Required fields enforced in migration + FormRequest
- SPAS uniqueness constraint must NOT be added

### Lifecycle
- Soft-deletes enabled
- Strike-off status column + restored_at column
- Archived instead of deleted after 5-year lifespan; scheduled archive job or manual archive action

### Relations
- One scholar → many `documents`
- One scholar → many `administrative_records` where applicable

---

## 3. Administrative Records

### Table: `administrative_records`

| Field | Type | Notes |
|-------|------|-------|
| id | unsigned bigint PK, auto-increment | |
| scholar_id | unsigned bigint FK → scholars.id, nullable | Administrative record may stand alone |
| record_type | string | |
| issued_at | date | |
| ... | ... | Add required metadata fields |

### Indexes
- `scholar_id` index
- Fulltext or indexed search fields

### Validation
- scholar_id allowed to be null if record is standalone
- Required metadata non-null

### Lifecycle
- Soft-delete enabled
- Archive after 10 years
- Strikable/restorable same as scholars

---

## 4. Documents and Versioning

### Tables
- `documents`
- `document_versions`

### `documents` fields
- id PK
- ownerable_id / ownerable_type polymorphic OR scholar_id/admin_record_id FKs
- file_path / storage_key required
- mime_type
- original_filename
- size_bytes
- uploaded_by user FK
- current_version FK → document_versions.id
- deleted_at soft-delete
- strike_off_status enum / bool
- restored_at nullable
- archived_at nullable

### `document_versions` fields
- id PK
- document_id FK → documents.id
- version_number required
- storage_path required
- mime_type
- size_bytes
- metadata json nullable
- uploaded_by user FK
- created_at

### Backend behavior
- Upload always creates new document or new document_version
- Duplicate upload flow must branch into:
  - cancel
  - keep history / append version
  - overwrite current version + preserve old version in document_versions
- Max upload size enforced: 10 MB
- Allowed types: PDF first, then image/* as needed
- Download action must stream from local storage with auth gate

### Indexes
- `documents.ownerable_id + ownerable_type`
- `documents.scholar_id`
- `documents.administrative_record_id`
- `document_versions.document_id`

---

## 5. Duplicate Merge Workflow

### Tables
- `merges`
- `merge_items`

### `merges` fields
- id PK
- source_scholar_id FK → scholars.id
- target_scholar_id FK → scholars.id
- status enum: proposed, compared, finalized, cancelled
- requested_by user FK
- decided_by user FK nullable
- decided_at nullable

### `merge_items` fields
- id PK
- merge_id FK → merges.id
- field_name string
- source_value text nullable
- target_value text nullable
- resolution enum: keep_source, keep_target, merged

### Backend actions
- Detect duplicate candidates
- Load side-by-side comparison payload
- Finalize merge: update/delete duplicate record, preserve chosen fields, log result
- Merge must be idempotent; rerun should not corrupt data

---

## 6. Search

### Backend requirements
- Global search across scholars, administrative_records, documents metadata
- Per-tab search inside Scholar 201 and Administrative Records tabs
- Indexed columns only in V1; avoid unbounded full-scan

### Tables/DB support
- Consider dedicated search index table or use MySQL fulltext if offline-capable
- Do not rely on external search service

---

## 7. Audit Logging

### Table: `audit_logs`

| Field | Type | Notes |
|-------|------|-------|
| id | unsigned bigint PK | |
| user_id | unsigned bigint FK → users.id nullable | system actions allowed |
| action | string | created/updated/deleted/restored/strikken/downloaded/merged/login |
| auditable_type | string | |
| auditable_id | unsigned bigint | |
| metadata | json | before/after, ip, user_agent if available |
| created_at | timestamp | |

### Backend requirements
- Log login and file actions minimum
- Log permission changes
- Keep immutable insert-only pattern

---

## 8. Offline-First and Queue

### Tables
- `offline_jobs` or `offline_queue`
- Optional: `sync_meta`

### Backend concerns
- Outbox pattern for local mutations when network/library unavailable
- Replay/retry strategy for failed jobs
- Do not block UI if queue backend fails
- Idempotency keys important

---

## 9. Reports / Dashboard

### Backend requirements
- PDF/image export from stored data
- Export job must run inside local Docker
- Cache output files or stream on demand
- Permission gate: Encoder can view; Admin can export

---

## 10. Docker / Infrastructure Expectations

### Required outputs
- `Dockerfile`
- `docker-compose.yml` with app + mysql + optional pma
- Healthcheck endpoint
- Volume mounts for uploads/storage
- Backup/restore scripts targeting mysql volume + storage volume

### Backend constraints
- App must start with one compose command
- No host-only dependencies
- Local-only MySQL; backup script must handle offline copy

---

## 11. QA / Test Surface

### Backend tests expected
- Feature: scholar CRUD
- Feature: administrative record CRUD
- Feature: upload with 10MB validation
- Feature: duplicate merge flow
- Feature: strike-off/restore
- Feature: archive lifecycle state transitions
- Feature: role gate enforcement
- Feature: search result correctness

---

## 12. Migration Order / Backend Readiness

1. users + roles + permissions
2. scholars
3. administrative_records
4. documents + document_versions
5. merges + merge_items
6. audit_logs
7. offline_queue tables
8. indexes after seeded data if needed

## 13. Backend Decision Log
- Default to additive schema changes; avoid replacing tables after they ship.
- Schema conflicts during implementation → Database Architect decides.
- No hard deletes in V1.
