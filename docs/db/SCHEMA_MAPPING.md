# Schema Mapping: Wakin dost_system → Mother Repo

Reference only. Mother repo migrations are canonical; do not run backend DDL against production.

| Backend Table / Concept | Mother Repo Canonical | Notes |
|---|---|---|
| `users` | `users` | Same shape. |
| Auth / User model | `App\Models\User` | Same. |
| `scholarship_programs` | `scholarships` | Renamed. |
| `scholarship_program_types` | `scholarship_types` | Renamed. |
| `files` (flat) | `documents` + `document_versions` | Mother repo uses polymorphic document storage with versioning; backend stores flat files with `file_path`. |
| `file_types` | `file_types` | Same. |
| `file_groups` | *(not carried)* | Dropped from canonical schema. |
| `scholars` | `scholars` | Same. |
| `audit_logs` | `audit_logs` | Same. |
| `clearance_statuses` | `clearance_statuses` | Same. |
| `courses` | `courses` | Same. |
| `schools` | `schools` | Same. |
| `regions` | `regions` | Same. |
| *(missing)* | `administrative_records` | New canonical entity; no backend equivalent. |
| Sample PDFs | `database/sample_pdfs/` | Archived here as fixtures; not part of runtime storage. |

## Divergence Summary
- **Naming:** scholarship_* → scholarship (singular) tables.
- **Storage:** Flat `files` table replaced by `documents` + `document_versions`.
- **Governance:** `administrative_records` exists only in mother repo.
- **Fixtures:** Real DOST PDFs are reference assets ported from Wakin's lab.
