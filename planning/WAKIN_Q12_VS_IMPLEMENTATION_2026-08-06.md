# Wakin Q1–Q12 ↔ Mother implementation + DB adoption guide

**Date:** 2026-08-06  
**Audience:** Wakin (`@WakenMac`) — backend / schema owner  
**Mother SoT:** `planning/STITCH_IMPLEMENTATION_PLAN.md` (locked from your PDF answers)  
**Related UI PR:** [#65](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/65) (Rui) — **request changes**; see `planning/PR65_REVIEW_2026-08-06.md`  
**Lab pin:** `WakenMac/DOST-RXI-OJT_SQL-Files` @ `b3510d9` (re-pin if `main` moved)

---

## How to read this

| Status | Meaning |
|--------|---------|
| ✅ Done on mother | Matches your Q answer in production schema/code |
| 🟡 Partial | Schema exists; save path / UI / observers not finished |
| ❌ Gap | Your decision is locked but not implemented (or contradicted) |
| 🚫 Reject | Lab pattern must **not** be ported |

---

## Side-by-side: Q1–Q12 vs mother today

| ID | Your answer (locked) | Mother implementation | Status | What you should do next |
|----|----------------------|------------------------|--------|-------------------------|
| **Q1** | File Group **CRUD after OJT** — seed taxonomy now; admin CRUD later | `file_groups` table + `FileGroup` model + `FileGroupSeeder`; **no** `/file-groups` admin UI | ✅ Seed / 🚫 CRUD deferred | Keep seeding by **slug**; do not add admin CRUD in V1 stitch PRs |
| **Q2** | **Yes** — ownership via `documents.documentable_*` morph | Morph on `documents`; Scholar / AdministrativeRecord owners | ✅ Schema | **Never** put `scholar_id` in `documents.metadata` for ownership. Lab `files` / metadata-ownership paths → rewrite to morph |
| **Q3** | `file_types.metadata_template` = form schema; `documents.metadata` = values only | Both columns exist (JSON); FileTypeSeeder fills templates | ✅ Schema / 🟡 Save path | Uploads must write **values** into `documents.metadata` only. PR #65 AddFile currently creates documents **without** metadata |
| **Q4** | Save → **always** new `document_versions` row | `document_versions` table exists; Show keep_history path versions | 🟡 Partial | Wire **every** AddFile / Edit / canvas save that replaces bytes to insert a version. PR #65 does **not** write versions |
| **Q5** | **Keep** three-way modal (cancel / keep_history / overwrite) | Still on `Scholars/Show` + `AdminRecords/Show` | ✅ | Do not remove. Canvas/editor (PR-E) must call same resolution options |
| **Q6** | jsPDF V1 = export pack + page reorder/delete/combine/insert | Issue [#55](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/55) open; DomPDF gone | ❌ Not started | Own **save/model** side of canvas; Rui owns Sortable UI (Q7) |
| **Q7** | Rui = Sortable UI/CSS; Wakin = save/model rules | Boundary documented; PR #65 is UI-heavy | 🟡 | Reject UI that saves into flat paths or skips `document_versions` |
| **Q8** | **Drop DomPDF** entirely | No `barryvdh/laravel-dompdf`; no `config/dompdf.php` on mother | ✅ | Do not reintroduce. Confirm lab clone has no DomPDF left ([#57](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/57)) |
| **Q9** | One **primary searchable key per file type** | Draft map: `docs/db/METADATA_PRIMARY_KEYS.md` | 🟡 Draft | Review/ACK the draft; search (PR-G / #41) blocked until keys locked |
| **Q10** | Observers on **all CRUD** → audit (phased) | Spatie route gates + policies on mother (#52); **no** DocumentObserver yet | 🟡 Authz done / ❌ Observers | Issue [#56](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/56): port File→**Document**Observer; map to mother `audit_logs` columns. **No** FileObserver |
| **Q11** | Invalid metadata vs template → **hard abort** before DB/storage | Issue [#54](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/54) **P0 open** | ❌ | Implement shared validator; block PR #65-style saves that skip template checks |
| **Q12** | Sortable = **SortableJS** page canvases (Vite), not table-column sort | Not in mother deps yet | ❌ | Add with PR-E; keep table-column sort as optional PR-H later |

---

## Lab → mother table map (adopt this mental model)

| Your lab (`dost_system` / WIP) | Mother canonical | Action |
|--------------------------------|------------------|--------|
| `files` (flat) | **`documents` + `document_versions`** | 🚫 Never merge flat `files`. Rewrite queries/seeders |
| ownership via `metadata.scholar_id` or file.scholar_id | `documents.documentable_type` + `documentable_id` | Rewrite |
| `file_groups` | `file_groups` | Port/seed by **slug** |
| `file_types` + `metadata_template` | same | Keep; **`year` column removed** — year lives in metadata values or scholar `year_of_award` |
| filled form fields | `documents.metadata` JSON | Values only |
| version history | `document_versions` | Required on keep_history / replace |
| DomPDF blades/config | deleted | Use jsPDF client path |
| `scholarship_programs*` naming in some WIP | mother `scholarships` / `scholarship_types` | Rename on port |
| Absolute Windows paths in FileSeeder | relative `database/sample_pdfs/**` | Fix before any merge of `db-integration` ([#58](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/58)) |

Full map: `docs/db/SCHEMA_MAPPING.md`.

---

## What changed on mother that affects your daily work

### Already on `master` (you can rely on these)

1. **Taxonomy** — `file_groups`, `file_types.file_group_id`, `file_types.metadata_template`, `documents.metadata` (#35).  
2. **AuthZ** — Spatie route middleware + policies + download 403 (#52). Encoder ≠ audit logs.  
3. **Sample PDFs** — `database/sample_pdfs/` + fixture helper (#46).  
4. **Offline queue scaffold** — table + `offline:replay` stub (#52 / #38); handlers still open (#59).  
5. **CI** — MySQL 8.4, pint, stylelint, `synthetic-smoke` (`/health`, auth, upload, download).  
6. **No DomPDF** on mother.

### Open stitch work you own / co-own

| Priority | Issue | Your role |
|----------|-------|-----------|
| P0 | [#54](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/54) metadata hard-abort | Implement validator used by all upload Livewire |
| P0 | [#58](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/58) remediate `db-integration` | Strip `files`, fix seeders/paths, align names — **do not blind-merge** |
| P1 | [#57](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/57) DomPDF confirm | Sweep lab + mother for leftovers |
| P1 | [#56](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/56) observers | Document/Scholar → `audit_logs` |
| P1 | [#55](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/55) jsPDF + Sortable | Save path + versions; Rui UI |

### PR #65 (Rui UI) — impact on your DB rules

|#65 behavior|Vs your locked rules|
|------------|-------------------|
| Creates `documents` with morph to Scholar | ✅ Aligns with Q2 |
| `FileType::firstOrCreate(..., ['year' => …])` | ❌ `year` column **dropped**; year is not fillable — dead/wrong; use taxonomy name + `metadata` |
| `FileType::where('is_available', …)` on Edit | ❌ **No `is_available` on `file_types`** — do not port that filter; availability is on lookups/scholars, not types |
| Delete by `documentable_id` only | ❌ Always scope deletes with **both** `documentable_type` + `documentable_id` |
| No `documents.metadata` write | ❌ Gaps Q3 / Q11 |
| No `document_versions` on upload | ❌ Gaps Q4 |
| Placeholder bytes `"DOST Document Content for …"` | ❌ Fake files in storage |
| Soft-delete docs without `DocumentPolicy::delete` / `strikeOffDocuments` | ❌ Auth matrix |
| Mock notifications / mock file editor / fake scholar fallback create | ❌ Must not ship as production save paths |

**Implication:** Do not treat #65 merge as “backend complete.” Backend handoff docs in that PR describe endpoints; **mother schema rules still win**.

---

## Adoption checklist (practical)

### 1. Fresh mother DB (local)

```bash
php artisan migrate:fresh --seed
php artisan test --group=smoke
```

Confirm: Super Admin `test@example.com` / `password`; FileGroups + FileTypes seeded; no `file_types.year` column.

### 2. When porting lab code

- Replace every `files` model/query with `Document` + morph.  
- On create/update of bytes: write `document_versions` when keeping history.  
- Validate payload against `file_types.metadata_template` **before** Storage::put / Document::create (Q11).  
- Resolve FileType by **name/slug**, not by inventing `year` on the type row.  
- Use relative sample paths only.

### 3. ACK items for Chan

- [ ] ACK `docs/db/METADATA_PRIMARY_KEYS.md` (Q9) or send corrections  
- [ ] ACK taxonomy seed list matches lab intent  
- [ ] Confirm lab HEAD still `b3510d9` or provide new pin SHA  
- [ ] Start #54 validator shared by AddFile / Show / Edit

### 4. What not to do

- Merge `origin/db-integration` as-is  
- Re-add DomPDF  
- Store ownership in metadata  
- Ship placeholder document content or mock scholars into prod flows  

---

## Suggested week order

1. **#54** hard-abort validator (unblocks honest upload)  
2. **#57** DomPDF sweep (quick)  
3. Align with Rui on #65 fixes (versions + metadata + no mocks in save)  
4. **#58** remediate lab branch  
5. **#56** observers  
6. **#55** canvas save rules with Rui  

---

## Pointers

| Doc | Path |
|-----|------|
| Locked plan | `planning/STITCH_IMPLEMENTATION_PLAN.md` |
| Schema map | `docs/db/SCHEMA_MAPPING.md` |
| Primary keys draft | `docs/db/METADATA_PRIMARY_KEYS.md` |
| Spatie matrix | `planning/SPATIE_ROLES_BASELINE.md` |
| PR #65 review | `planning/PR65_REVIEW_2026-08-06.md` |
| Issues list | `planning/HANDOFF_GITHUB_ISSUES.md` |
