# AGY Handoff — Backend-to-Mother Stitch Investigation

Task: re-check Wakin’s repo changes since the last investigation and produce a final implementation plan for the backend-to-mother stitch.

Workdir: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage`

## Sources to read

1. `planning/project_bible_v02_extracted.md` — Wakin’s answers to Q1–Q12.
2. `planning/_backend_stitch_components.md` — locked stitch decisions.
3. `planning/jspdf_versioning.md` — jsPDF/SortableJS versioning contract.
4. `_backend_scratch/wakin/` — Wakin’s cloned OJT lab.
   - Focus paths:
     - `database/migrations/`
     - `database/seeders/`
     - `app/Models/`
     - `app/Observers/`
     - `database/sample_pdfs/`
5. Mother repo paths:
   - `database/migrations/`
   - `app/Models/`
   - `resources/views/pdf/`
   - `public/build/manifest.json` if present
   - `package.json`, `vite.config.js`

## Tasks

1. Diff Wakin’s repo against the halfway doc claims:
   - Confirm `file_groups`, `file_types`, `files` migrations exist.
   - Confirm `FileTypeSeeder` and `metadata_template`.
   - Confirm `FileObserver`, `ScholarObserver`.
   - Confirm `sample_pdfs/` fixtures.
   - Confirm DomPDF scaffolding exists but is unused.
   - Confirm jsPDF + SortableJS client code.
2. Check mother repo for:
   - Existing `documents` or `document_versions` migrations.
   - Existing models or controllers that already reference `documents`.
   - Vite/JS dependencies related to jsPDF/Sortable/Dompdf.
   - Any stale PDF export downloads path.
3. Produce a scoped `IMPLEMENTATION_PLAN_AGY.md` in `planning/`:
   - Section 1: Confirmed findings (Wakin lab vs mother).
   - Section 2: Schema conflict resolution (from `_backend_stitch_components.md`).
   - Section 3: jsPDF/SortableJS port plan (from `jspdf_versioning.md`).
   - Section 4: PR slice order with exact file paths and owners.
   - Section 5: Risks + mitigations.
   - Section 6: Open items + what needs final lock before stitch delivery.
4. Append entries to `planning/AGENTIC_CHANGELOG.md` for every meaningful finding.
5. Do not push, commit, or modify code; investigation and planning only.
6. Hard constraint: canonical target tables in mother are `documents` + `document_versions`. Do not recommend shipping or porting Wakin’s flat `files` table into mother.
