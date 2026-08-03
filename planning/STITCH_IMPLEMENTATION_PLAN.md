# Backend → Mother Stitch — Implementation Plan

**Status:** LOCKED for execution (Wakin Q1–Q12 answered 2026-07-28/29)  
**Canonical branch:** `feat/be-stitch-backend-to-mother` (from `origin/master`)  
**Mother HEAD (execution baseline):** `142b90f` — Merge PR #28 FS-08 hardening (`origin/master`)  
**Wakin lab HEAD (pinned):** `b3510d9` — `Created Observers` (`WakenMac/DOST-RXI-OJT_SQL-Files` @ `main`)  
**Date locked:** 2026-07-29 · **Reconfirmed:** 2026-08-04  
**Owners:** Chan (orchestration/CI), Wakin (schema/save path/observers/search), Rui (SortableJS/jsPDF UI)

---

## 0. Baselines checked (2026-08-04)

### Mother (`xxch4nnn/DOST-S-T-SECTION-PROJECT`)

| Item | Value |
|------|-------|
| `origin/master` | `142b90f` (PR #28 merged) |
| Stitch branch | `feat/be-stitch-backend-to-mother` rebased onto master |
| Already on master | `documents` + `document_versions`, Livewire Scholars/AdminRecords, Spatie, three-way duplicate modal, Docker/CI, FS-08 hardening |
| Parallel WIP (do **not** merge as-is) | `origin/db-integration` @ `c7ffec9` — see §0.1 |

### Wakin lab (`WakenMac/DOST-RXI-OJT_SQL-Files`)

| Item | Value |
|------|-------|
| Latest commit | `b3510d9` Created Observers (unchanged as of 2026-08-04) |
| Port candidates | `file_groups` / `file_types.metadata_template` seeders, sample PDFs, observers (adapt), jsPDF canvas notes |
| Never port | flat `files` table / `File` model as canonical storage |

If Wakin `main` moves past `b3510d9`, stop and re-pin before more ports.

### 0.1 `db-integration` — REJECT blind merge

`origin/db-integration` contains real UI/search/upload work **and** hard-constraint violations:

| Violation | Evidence |
|-----------|----------|
| Flat `files` table | `database/migrations/2026_07_20_061543_create_files_table.php`, `app/Models/File.php` |
| Absolute Windows paths in seeder | `FileSeeder` points at Wakin machine paths |
| Parallel scholarship naming | `ScholarshipProgram*` vs mother `scholarships` |
| No jsPDF deps yet | `package.json` on that tip |

**Action:** cherry-pick **safe UI** pieces later onto stitch PRs after remediating schema. Do **not** squash-merge `db-integration` onto `master`.

---

## 1. Locked decisions (from Wakin PDF answers)

| ID | Decision |
|----|----------|
| Q1 | File Group **CRUD after OJT** — seed taxonomy now; admin CRUD later |
| Q2 | **Yes** — `documents.documentable_*` morph ownership |
| Q3 | `file_types.metadata_template` = form schema; `documents.metadata` = filled values only |
| Q4 | Save → **always new `document_version`** (“no pure deletes”) |
| Q5 | **Keep** three-way modal (cancel / keep_history / overwrite), GDrive-style |
| Q6 | jsPDF V1 = **(a)+(b)** export pack + page reorder/delete/combine/insert |
| Q7 | **Rui** owns Sortable UI/CSS; SortableJS does order; Wakin owns save/model rules |
| Q8 | **Drop DomPDF** entirely |
| Q9 | One **primary searchable key per file type**; Chan maps specifics in PR-C docs |
| Q10 | Observers on **all CRUD surfaces** for audit (phased) |
| Q11 | Invalid metadata vs template → **hard abort** before DB/storage |
| Q12 | Sortable = **SortableJS** page canvases (Vite/JS import), not table-column sort |

### Hard constraints (non-negotiable)

1. Canonical storage: **`documents` + `document_versions` only** — never ship Wakin’s flat `files` table.  
2. Drop DomPDF (`config/dompdf.php`, compiled PDF blade if present).  
3. Keep three-way duplicate modal.  
4. Every committed replace/save that keeps history writes a **`document_versions`** row.  
5. No `scholar_id` ownership inside `documents.metadata`.  
6. Column name on types: **`metadata_template`**.

---

## 2. Comparison vs Hermes / Antigravity handoff

Source: `planning/ANTIGRAVITY_STITCH_HANDOFF.md` (+ AGY/FINAL drafts on this branch).

| Topic | Hermes / Antigravity | This plan (adopt / revise) |
|-------|----------------------|----------------------------|
| Canonical tables | `documents` + `document_versions` | **Adopt** |
| Drop flat `files` | Yes | **Adopt** |
| Drop DomPDF | Yes | **Adopt** (Q8) |
| Keep 3-way modal | Yes | **Adopt** (Q5) |
| `metadata_template` name | Yes | **Adopt** (Q3) |
| Migration strategy | **Replace** legacy `095051` / copy groups as `095052` (collides with scholars) | **Revise:** **additive** `2026_07_29_*` migrations only |
| Touch Livewire show blades | Forbidden in handoff pass | **Adopt for schema/seed slices**; Show edits in later scoped PRs |
| Observers | File+Scholar now | **Expand** per Q10; rename File→Document; **no** FileObserver on mother |
| File Group CRUD | Implied with taxonomy | **Defer UI** (Q1); seed only |
| jsPDF / SortableJS | Deps early | **Adopt**; Rui UI in PR-E |
| Changelog | AGENTIC + execution log | **Adopt + institutionalize** (root CHANGELOG + CONTRIBUTING/AGENTS) |
| Push/PR | Antigravity: no push | **Open reviewable PRs** when green |

**Verdict:** Adopt Hermes hard constraints; **revise** migration mechanics and File Group CRUD timing; **reject** `db-integration` merge until files-table / seeder / naming fixed.

---

## 3. PR slice order (execution)

Work on `feat/be-stitch-backend-to-mother` (or stacked `feat/be-0N-*`). Reviewers: Wakin, Rui, Miguel (CODEOWNERS).

### PR-A — Docs + changelog bootstrap *(this PR / in flight)*
- Root `CHANGELOG.md`, AGENTIC/STITCH logs, CONTRIBUTING/AGENTS rules
- Locked `STITCH_IMPLEMENTATION_PLAN.md` + Hermes comparison
- Pin Wakin `b3510d9`; document `db-integration` reject
- Optional: `docs/db/SCHEMA_MAPPING.md`

### PR-B — PDF fixtures
- Copy `database/sample_pdfs/**` from Wakin lab (~125 MB) into private mother
- Fixture helper / DocumentSeeder for tests (paths relative, not absolute Windows)

### PR-C — Taxonomy schema + seeders *(in flight with PR-A)*
- Additive: `file_groups`; alter `file_types` (`file_group_id`, `metadata_template`, drop `year`)
- Additive: `documents.metadata` JSON nullable
- Models: `FileGroup`; update `FileType` + `Document`
- Seeders: `FileGroupSeeder`, rich `FileTypeSeeder` (resolve groups by slug; no hard-coded IDs)
- Primary key map draft `docs/db/METADATA_PRIMARY_KEYS.md`
- Update upload tests that still pass `year` on FileType
- **No** File Group admin CRUD; **no** flat `files`

### PR-D — Reactive upload validation (hard abort)
- Shared validator vs `metadata_template` (Q11)
- Livewire dynamic fields; extend upload feature tests

### PR-E — jsPDF + SortableJS canvases
- npm deps; Rui UI; Wakin save → always `document_versions`; keep 3-way modal; **no DomPDF**

### PR-F — Observers + audit alignment
- `DocumentObserver`, `ScholarObserver`, then lookups; map to mother `audit_logs` columns

### PR-G — Intelligent search
- Morph + group/type + one primary metadata key per type (Q9)

### PR-H (optional) — table-column sortable (separate from canvas)

---

## 4. File path cheat sheet

| Port from Wakin | Mother destination | Notes |
|-----------------|-------------------|-------|
| file_groups migration | `2026_07_29_*_create_file_groups_table.php` | Additive |
| file_types shape | alter via `2026_07_29_*` | Keep history |
| `FileGroup.php`, `FileType.php` | `app/Models/` | `documents()` not `file()` |
| seeders | `database/seeders/` | Slug-based group ids |
| `sample_pdfs/**` | `database/sample_pdfs/**` | PR-B |
| FileObserver → DocumentObserver | `app/Observers/` | PR-F |
| DomPDF | **Delete if present** | Q8 |

---

## 5. Verification (each PR)

```bash
php artisan migrate:fresh --seed
php artisan test
vendor/bin/pint --test
npm run build   # when JS deps change
```

Confirm: no `files` table; `file_groups` + `metadata_template` + `documents.metadata`; DomPDF gone.

---

## 6. Risks & mitigations

| Risk | Mitigation |
|------|------------|
| Merging `db-integration` | Block until files table / absolute paths / ScholarshipProgram removed |
| Hermes replace-migration | Additive only |
| Observer column mismatch | Align to mother `audit_logs`; tests |
| Rui/Wakin Show conflicts | Q7 boundary |
| 125 MB sample PDFs | Private repo; separate PR-B |

---

## 7. Changelog institutionalization

- **`CHANGELOG.md`** — product Keep-a-Changelog `[Unreleased]`
- **`planning/AGENTIC_CHANGELOG.md`** — agent/ops trail
- **`planning/STITCH_EXECUTION_LOG.md`** — stitch steps
- Rules live in `CONTRIBUTING.md`, `AGENTS.md`, `.cursorrules`

---

## 8. Immediate next actions (2026-08-04)

1. Land PR-A/C (plan + taxonomy) on `feat/be-stitch-backend-to-mother` → open PR → review → squash to master.  
2. PR-B sample PDFs from `_backend_scratch/wakin`.  
3. Remediate or discard `db-integration` files-table path.  
4. Merge green Dependabot minors; **hold** concurrently major (#31) for Node/ESM review.  
5. Do **not** merge stitch to master until migrate:fresh --seed + tests green + Wakin ACK on taxonomy.

---

## 9. Out of scope

- Lesson* / `sample_project`  
- Flat `files` table  
- DomPDF  
- Renaming `scholarships` → `scholarship_programs`  
- Sanctum API  
- File Group admin CRUD UI (post-OJT)
