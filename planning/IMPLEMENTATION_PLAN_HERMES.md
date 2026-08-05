# IMPLEMENTATION_PLAN_HERMES.md

Source: Hermes investigation against Wakin’s PDF answers, `_backend_scratch/wakin` clone, and mother repo state on `feat/fe-08-fullstack-hardening-v2` (`db34ff9`).

---

## 1. Confirmed findings

### Wakin lab (`WakenMac/DOST-RXI-OJT_SQL-Files`, HEAD `b3510d9`)

| Finding | Evidence path | Status |
|---|---|---|
| `file_groups` migration | `_backend_scratch/wakin/dost_system/database/migrations/2026_07_20_061506_create_file_groups_table.php` | ✅ |
| `file_types` migration | `_backend_scratch/wakin/dost_system/database/migrations/2026_07_20_061515_create_file_types_table.php` | ✅ |
| `metadata_template` column on `file_types` | same file; json, non-null | ✅ |
| `files` migration | `_backend_scratch/wakin/dost_system/database/migrations/2026_07_20_061543_create_files_table.php` | ✅ |
| `FileTypeSeeder` | `_backend_scratch/wakin/dost_system/database/seeders/FileTypeSeeder.php` | ✅ |
| primary metadata keys | seeder fields: `scholar_id`, `series_number`, `report_number`, `payroll_number` | ✅ |
| `FileObserver` | `_backend_scratch/wakin/dost_system/app/Observers/FileObserver.php` | ✅ |
| `ScholarObserver` | `_backend_scratch/wakin/dost_system/app/Observers/ScholarObserver.php` | ✅ |
| `sample_pdfs/` fixtures | 20 PDFs across `Annual_Financial_Reports`, `Certificate_Of_Registration`, `Endorsements`, `Memorandums`, `Payrolls`, `Quarterly_Financial_Reports` | ✅ |
| DomPDF scaffold | `_backend_scratch/wakin/dost_system/resources/views/pdf/compiled_images.blade.php` + `config/dompdf.php` | ✅ exists, unused per DTR |
| jsPDF + SortableJS | `package.json` has `sortablejs: ^1.15.7`; `resources/js/app.js` imports `Sortable from 'sortablejs'` | ✅ |
| observer coverage list | Wakin’s Q10 answer: `scholarshipPrograms`, `scholarshipProgramTypes`, `Regions`, `Documents`, `FileTypes`, `FileGroups`, `Files`, `Migrations`, `Scholars` | ✅ |

### Mother repo (`xxch4nnn/DOST-S-T-SECTION-PROJECT`, HEAD `db34ff9`)

| Finding | Evidence path | Status |
|---|---|---|
| `documents` migration | `database/migrations/2026_07_15_095054_create_documents_table.php` | ✅ exists |
| `document_versions` migration | `database/migrations/2026_07_15_095055_create_document_versions_table.php` | ✅ exists |
| `Document` model | `app/Models/Document.php` | ✅ |
| `DocumentVersion` model | `app/Models/DocumentVersion.php` | ✅ |
| `DocumentController@download` | `app/Http/Controllers/DocumentController.php` + `routes/web.php` | ✅ |
| stale `file_types` migration | `database/migrations/2026_07_15_095051_create_file_types_table.php` — columns `name`, `year`; no `metadata_template`, no `file_group_id` | ⚠️ conflict |
| `file_groups` migration | not present | ❌ missing |
| `files` migration | not present | ❌ missing |
| mother-side Observer coverage | none for document/scholar/admin-record CRUDAudit | ❌ missing |

---

## 2. Schema conflict resolution

### Conflict A: `file_types` column drift

- Wakin: `file_groups.id` → `file_types.file_group_id`, `name`, `metadata_template` (json).
- Mother legacy: `name`, `year`. No `metadata_template`, no FK.
- **Decision**: Replace/upgrade mother `file_types` migration to Wakin’s shape. Lock column name = `metadata_template` (Q3 confirmed).

### Conflict B: `files` vs `documents` + `document_versions`

- Wakin flat `files` table must not ship in mother.
- Mother already has `documents` with `documentable_*` morph, `status` enum, `uploaded_by`.
- **Decision**: Drop `files` in mother. Keep `documents` + `document_versions` as the canonical path. Add `documentable_*` coverage to Observers.

### Conflict C: DomPDF

- Wakin scaffold exists but unused. Mother has no DomPDF dependencies.
- **Decision**: target for full drop in V1 unless a report path explicitly requires headless generation after stitch.

---

## 3. jsPDF/SortableJS port plan

- Add `sortablejs` to mother `package.json` (Wakin already has `^1.15.7`; mother currently has none).
- Import `Sortable` in mother’s Vite entry.
- Rui owns sortable UI CSS data shape; Wakin owns save path + `document_versions` write rules.
- Save always creates new `document_version` row; no pure overwrite.
- Keep three-way duplicate modal (Q5 confirmed).

---

## 4. PR slice order with exact paths

| Order | PR | Title | Mother paths touched | Owner |
|---|---|---|---|---|
| 1 | `feat/be-01-db-docs` | Replace stale `file_types`, add `file_groups`, align `documents` + `document_versions` to locked schema | `database/migrations/2026_07_15_095051_create_file_types_table.php`, keep `2026_07_15_095054/_055` after validation | Wakin |
| 2 | `feat/be-02-pdf-fixtures` | Copy `database/sample_pdfs/` from Wakin | `database/sample_pdfs/` | Wakin |
| 3 | `feat/be-03-filetype-metadata` | Port `FileTypeSeeder` + reactive upload form rules | `database/seeders/FileTypeSeeder.php` + Livewire form | Wakin + Rui |
| 4 | `feat/be-04-jspdf-export` | jsPDF + SortableJS canvas pipeline + download path | `resources/js/`, `resources/views/`, `routes/web.php`, `DocumentController` | Rui + Wakin |
| 5 | `feat/be-05-observers` | Port `FileObserver`, `ScholarObserver`, add AdminRecord/Observer coverage | `app/Observers/` | Wakin |
| 6 | `feat/be-06-intelligent-search` | Indexed search on primary metadata keys per data type | `app/Models/`, search scope | Wakin |
| 7 | `feat/ui-07-sortable` | Table-column sort if still required; separate from canvas sort | FE only if needed | Rui |

---

## 5. Risks + mitigations

| Risk | Mitigation |
|---|---|
| Mother `documents`/`document_versions` columns mismatch Wakin’s write assumptions | Diff exact column lists in PR 1 before merging; update `DocumentController` paths to match stored_filename |
| `file_types` legacy migration blocks in-flight features | Coordinate rebuild so no live user depends on old `year` column |
| DomPDF not fully dead code | Explicitly delete scaffold files before PR 4 merge |
| jsPDF + SortableJS version mismatch | Pin compatible versions; regression test page insert + reorder |
| Invalid metadata accidentally saved | Hard-fail in observer/FormRequest before DB write (Q11 confirmed) |
| Change tracking gaps | Enforce `AGENTIC_CHANGELOG.md` append on every push/pull/PR merge |

---

## 6. Open items before stitch delivery

- [ ] Validate mother `documents`/`document_versions` column names against Wakin’s expected save path; adjust if needed.
- [ ] Confirm exact SortableJS Vite import pattern with Rui.
- [ ] Confirm primary searchable metadata keys for each data type with Wakin (currently only seeder examples exist).
- [ ] Lock PR owners for PRs 4–6 before authoring.
- [ ] Decide whether to remove DomPDF scaffold now or defer to post-V1 cleanup.
