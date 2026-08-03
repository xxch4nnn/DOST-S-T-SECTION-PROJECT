# Primary searchable metadata keys (Q9 draft)

One primary key per file type for intelligent search. Refine with Chan/Wakin before PR-G.

| File type | Primary key field | Notes |
|-----------|-------------------|-------|
| Most scholarly types | `scholar_id` | Ownership still via morph; this is search facet only |
| Certificate of Registration / Grades | `scholar_id` (+ semester/year secondary) | |
| Memorandums | `series_number` | |
| Annual Financial Reports | `report_number` | |
| Payrolls | `payroll_number` | |
| Endorsements | `academic_year` | Confirm with Wakin |
| Communications | `title` | Confirm with Wakin |

Do not store morph ownership inside `documents.metadata`.
