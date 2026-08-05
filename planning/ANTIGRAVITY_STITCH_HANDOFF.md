# ANTIGRAVITY_STITCH_HANDOFF.md

Purpose: exact execution contract for antigravity to perform the backend-to-mother stitch. This is planning + prep only. Do not merge to remote until reviewed.

Workdir: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage`

## Hard constraints (non-negotiable)

1. Canonical tables: `documents` + `document_versions` only.
2. Do not ship, port, or reference Wakin’s flat `files` table in mother.
3. Drop DomPDF completely.
4. Keep three-way duplicate modal (Keep History / Overwrite / Cancel).
5. Every save/new version writes a new `document_versions` row.
6. `file_types` column name is `metadata_template` (not `metadata`).
7. `metadata` JSON on `documents` stores form fields only; never `scholar_id` linkage.
8. No changes to `resources/views/livewire/scholars/show.blade.php` or `resources/views/livewire/admin-records/show.blade.php` shape in this handoff pass.

---

## 0. Pre-flight checks

Run before any code changes:

```bash
git status --short
git diff --name-only
git stash list
```

### DESIGN.md deletion
There is an unstaged deletion of `DESIGN.md` in the mother repo. Pick ONE:

- **Option A (commit):** `git add DESIGN.md && git commit -m "chore(docs): remove stale DESIGN.md before stitch"`
- **Option B (stash):** `git stash push -m "pre-stitch stash DESIGN.md deletion"`

If neither is appropriate, stop and ask Chan.

### Verify Wakin baseline
```bash
git -C _backend_scratch/wakin fetch origin
git -C _backend_scratch/wakin rev-parse HEAD
git -C _backend_scratch/wakin log --oneline origin/main -3
```

Expected HEAD: `b3510d9 Created Observers`. If HEAD differs, stop and report drift.

### Ensure changelog hook is ready
Confirm `planning/AGENTIC_CHANGELOG.md` exists. If missing, create it with at least one baseline entry.

---

## 1. Branch strategy

```bash
git checkout feat/fe-08-fullstack-hardening-v2
git pull origin feat/fe-08-fullstack-hardening-v2
git checkout -b feat/be-stitch-backend-to-mother
```

All PR slices below are prepared on `feat/be-stitch-backend-to-mother`. Do not push until all 7 slices are verified locally.

---

## 2. Migration replumbing (schema lock)

These operations lock the canonical schema. Do not skip renames.

```bash
# 2a. Stale file_types migration must be replaced.
# The legacy migration is:
#   database/migrations/2026_07_15_095051_create_file_types_table.php
# It currently has columns: name, year (no file_group_id, no metadata_template).

# Delete legacy migration and replace with Wakin's shape.
rm database/migrations/2026_07_15_095051_create_file_types_table.php
cp _backend_scratch/wakin/dost_system/database/migrations/2026_07_20_061515_create_file_types_table.php \
   database/migrations/2026_07_15_095051_create_file_types_table.php

# 2b. Add file_groups migration from Wakin.
cp _backend_scratch/wakin/dost_system/database/migrations/2026_07_20_061506_create_file_groups_table.php \
   database/migrations/2026_07_15_095052_create_file_groups_table.php

# 2c. Keep canonical documents + document_versions migrations as-is.
#   2026_07_15_095054_create_documents_table.php
#   2026_07_15_095055_create_document_versions_table.php
# Verify they match FINAL_SETTLED_IMPLEMENTATION_PLAN.md Section 2 shapes.
```

### Models to add/modify

```bash
# New taxonomy models from Wakin
cp _backend_scratch/wakin/dost_system/app/Models/FileGroup.php app/Models/FileGroup.php
cp _backend_scratch/wakin/dost_system/app/Models/FileType.php app/Models/FileType.php

# Update app/Models/FileType.php fillable/relations to match Wakin's shape exactly.
# Ensure metadata_template cast is 'array'.
```

---

## 3. Seeders port

```bash
cp _backend_scratch/wakin/dost_system/database/seeders/FileGroupSeeder.php database/seeders/FileGroupSeeder.php
cp _backend_scratch/wakin/dost_system/database/seeders/FileTypeSeeder.php database/seeders/FileTypeSeeder.php

# Update database/seeders/DatabaseSeeder.php to call:
#   FileGroupSeeder::class,
#   FileTypeSeeder::class,
```

---

## 4. Observers port

```bash
cp _backend_scratch/wakin/dost_system/app/Observers/FileObserver.php app/Observers/FileObserver.php
cp _backend_scratch/wakin/dost_system/app/Observers/ScholarObserver.php app/Observers/ScholarObserver.php

# Register observers in app/Providers/AppServiceProvider.php boot():
#   File::observe(FileObserver::class);
#   Scholar::observe(ScholarObserver::class);
#   Document::observe(DocumentObserver::class);   # new
#   AdministrativeRecord::observe(AdministrativeRecordObserver::class); # new if table exists
```

---

## 5. PDF fixtures port

```bash
cp -r _backend_scratch/wakin/dost_system/database/sample_pdfs database/sample_pdfs
```

Verify directory tree in `database/sample_pdfs/` matches Wakin lab.

---

## 6. Frontend dependency additions

Edit `package.json` in mother repo root. Add:

```json
"dependencies": {
  "jspdf": "^2.5.1",
  "sortablejs": "^1.15.7"
}
```

Edit `resources/js/app.js`:

```javascript
import Sortable from 'sortablejs';
import { jsPDF } from 'jspdf';
window.Sortable = Sortable;
window.jsPDF = jsPDF;
```

Do NOT create any new blade views in this handoff pass.

---

## 7. Audit/logging alignment

### Mother audit_logs schema

`audit_logs` columns: `user_id`, `action`, `record_type` (string), `record_id` (int), `before_payload`, `after_payload`, `ip_address`, timestamps.

Observer writes must match this shape exactly. Do NOT introduce polymorphic `auditable_type` / `auditable_id`.

---

## 8. Do not touch list

For this handoff pass, do NOT modify:

- `resources/views/livewire/scholars/show.blade.php`
- `resources/views/livewire/admin-records/show.blade.php`
- `resources/views/pdf/compiled_images.blade.php` (delete this file instead)
- `config/dompdf.php` (delete this file)

---

## 9. Verification checklist

Run after all slices are applied:

```bash
php artisan migrate:fresh --seed
php artisan test
npm run build
```

Confirm:

- [ ] `php artisan migrate:fresh --seed` passes without SQL errors.
- [ ] `file_groups` table exists with `name`, `slug`.
- [ ] `file_types` table exists with `file_group_id`, `name`, `metadata_template`.
- [ ] `documents` table exists with `documentable_id`, `documentable_type`, `file_type_id`, `status`, `uploaded_by`, soft deletes.
- [ ] `document_versions` table exists with `document_id`, `version_number`, `replaced_by_user_id`, `stored_filename`, `original_filename`, `file_size_kb`.
- [ ] `database/sample_pdfs/` contains Wakin's fixture tree.
- [ ] `package.json` contains `jspdf` and `sortablejs`.
- [ ] `resources/js/app.js` exports `window.Sortable` and `window.jsPDF`.
- [ ] `resources/views/pdf/compiled_images.blade.php` does NOT exist.
- [ ] `config/dompdf.php` does NOT exist.
- [ ] `app/Observers/FileObserver.php`, `ScholarObserver.php`, `DocumentObserver.php` exist and write to `audit_logs` with correct columns.
- [ ] `php artisan test` passes.

---

## 10. Output requirements

Do not write results to stdout only. Append all findings, diffs, and verification results to:

- `planning/AGENTIC_CHANGELOG.md`
- `planning/STITCH_EXECUTION_LOG.md` (create if missing)

Format each entry as:

```
- **Date:** YYYY-MM-DD
- **Actor:** Antigravity
- **Repo:** `xxch4nnn/DOST-S-T-SECTION-PROJECT` @ `feat/be-stitch-backend-to-mother`
- **Action:** investigation / file created / file modified / test result
- **Commit:** N/A (do not commit during this handoff)
- **Summary:** one-line description
- **Linked:** file path
```

---

## 11. Boundary

This handoff covers investigation and planning artifact preparation only. Do not open PRs, push branches, merge, or deploy.
