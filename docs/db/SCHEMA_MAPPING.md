# Schema mapping — Wakin lab → mother

| Wakin (`dost_system`) | Mother (canonical) | Notes |
|----------------------|--------------------|-------|
| `files` | **Do not port** → use `documents` + `document_versions` | Hard constraint |
| `file_groups` | `file_groups` | Seed only in V1 (no admin CRUD yet) |
| `file_types` (+ `metadata_template`) | `file_types` | Additive migration; drop unused `year` column |
| ownership via metadata / scholar_id on file | `documents.documentable_*` morph | Never put ownership in `documents.metadata` |
| form field values | `documents.metadata` JSON | Values only; schema lives on type |
| version history | `document_versions` | Every keep_history / canvas save |
| DomPDF | **Removed / never add** | jsPDF client path |
| `scholarship_programs` naming in some WIP | Keep mother `scholarships` / `scholarship_types` | |

Primary searchable keys (Q9 draft): see `METADATA_PRIMARY_KEYS.md`.
