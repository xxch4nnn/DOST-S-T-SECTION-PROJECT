# _backend_stitch_components.md

Purpose: canonical map of schema conflicts and port decisions between Wakin’s OJT lab (`WakenMac/DOST-RXI-OJT_SQL-Files`) and the mother repo (`xxch4nnn/DOST-S-T-SECTION-PROJECT`).

---

## Conflict 1: files table vs. documents + document_versions

| Component | Wakin’s OJT lab | Mother repo | Decision |
|---|---|---|---|
| Storage table | `files` (flat) | `documents` (polymorphic `documentable_*`) | **Port to `documents`**; drop `files` table in mother |
| Ownership | `metadata.scholar_id` (JSON linkage) | `documents.documentable_id/type` morph | **Use morph FKs only** |
| Type taxonomy | `file_types` + `file_groups` | none yet | **Keep both** (`file_types.metadata_template`, `file_groups`) |
| Soft deletes | `files.deleted_at` | `documents.deleted_at` | Align — soft deletes in `documents` |
| Version history | none (FileObserver only) | `document_versions` (`version_number`, `replaced_by_user_id`) | **Add `document_versions`**; always create new version on save |
| Duplicate modal | None | Three-way modal: cancel / keep_history / overwrite | **Keep modal** (Wakin’s choice) |

Locked rules:
- No `metadata.scholar_id` linkage inside documents.
- `file_types` column name is **`metadata_template`** (not `metadata`).
- Save always writes a new `document_version`; never pure overwrite.

---

## Conflict 2: PDF rendering

| Component | Wakin’s OJT lab | Mother repo | Decision |
|---|---|---|---|
| Primary PDF engine | jsPDF (+ SortableJS canvases) | none yet | **Port jsPDF + SortableJS pipeline** |
| Secondary engine | DomPHP scaffolding (unused) | none | **Drop DomPDF from V1** |

Locked rules:
- V1 canvas target = **(a) + (b)**: export scholar docs as PDF; sortable pages; insert pages between existing ones.
- SortableJS is imported via Vite (not table-column sort).
- Rui owns sortable UI; Wakin owns save path + model rules.

---

## Conflict 3: Observers / audit logs

| Observer target | Wakin’s OJT lab | Mother repo | Decision |
|---|---|---|---|
| File | `FileObserver` | none | Port |
| Scholar | `ScholarObserver` | none | Port |
| Others | unspecified | none | Add observers for **all CRUD tables**: `scholarshipPrograms`, `scholarshipProgramTypes`, `Regions`, `Documents`, `FileTypes`, `FileGroups`, `Files` (in lab), `Migrations`, `Scholars`, + any future CRUD tables |

Locked rules:
- Invalid metadata = **hard fail / abort save** before DB write.
- Observer layer handles audit log creation.
- Primary searchable metadata keys: one primary key per metadata type (e.g. `scholar_id`, `series_number`, `report_number`, `payroll_number`).

---

## PR slice order (tentative lock)

1. `feat/be-01-db-docs` — `documents` + `document_versions` migrations + models
2. `feat/be-02-pdf-fixtures` — `database/sample_pdfs/` from Wakin’s repo
3. `feat/be-03-filetype-metadata` — `file_groups` + `FileType` (`metadata_template`) + seeders + reactive upload form
4. `feat/be-04-jspdf-export` — jsPDF + SortableJS canvas pipeline (Rui FE, Wakin BE)
5. `feat/be-05-observers` — `FileObserver` + `ScholarObserver` ports + new observer coverage
6. `feat/be-06-intelligent-search` — `search()` + indexed metadata keys
7. `feat/ui-07-sortable` — table-column sort if still needed (separate from canvas SortableJS)

---

## Open items

- [ ] Confirm primary searchable metadata keys with Wakin (not yet locked)
- [ ] Confirm SortableJS Vite package name exact import
- [ ] Lock PR ownership by Q7 before PRs 4–6 open
