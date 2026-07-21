# Bible Center — remaining fixes to apply
## 4. Search scope status
- File: Bible Center, Section 6.1
- Current:
  - Header: `6.1 Search — Answered`
  - Followon: `Search fields, advanced filtering, partial matching, search priorities.`
- Replace with:
  - `6.1 Search — Client decision required`
  - `V1 waits for client confirmation: choose either full global search across the full text-searchable field set, or a narrower V1 subset. Fields under consideration: scholar metadata, institution/folder metadata, document presence/status.`

## 10.2 ERD update (A.6/D8)
- File: Bible Center, Section 10.2
- Current:
  - Header: `10.2 ERD`
  - Body: `[OPEN] Not yet produced. To come after Section 9 workflows are validated and the Database Architect proposes a schema for backend review.`
- Replace with:
  - `10.2 ERD — Draft plan with additive extensions`
  - `[DECISION] Use the architect's draft as the base; add administrative_records, documents/document_versions, and audit_logs for V1 review. Full ERD after validation with the Database Architect.`

## 2.1 Functional requirements block
- File: Bible Center, Section 2.1
- Insert after the header line:
  - `- Scholar 201 CRUD with required-field validation`
  - `- Document upload with metadata and 10MB limit`
  - `- Global search over core scholar/document fields`
  - `- Administrative records upload + metadata`
  - `- Strike-off/restore with soft-delete behavior`
  - `- Duplicate upload handling: cancel / keep history / overwrite`
  - `- Role-based access control for Super Admin / Admin / Encoder`

---
# TEAM_WORKFLOW.md — suggested additions for 7–8
## 7. Archive/compaction rule
Add under Shared Pool Rules:
- `Archived items in the Bible must be tagged [ARCHIVED] and moved to an Archive appendix.`
- `Removed items must not be deleted; timestamps must be preserved.`

## 8. Deployment gap in acceptance criteria
Add to Acceptance Criteria item 5/6/7 block:
- `5. Docker Compose brings up app + database cleanly and is the deployment path.`

# Exact copy-ready insertion bullets for 2.1
Copy this block into Bible Center Section 2.1:
- Scholar 201 CRUD with required-field validation
- Document upload with metadata and 10MB limit
- Global search over core scholar/document fields
- Administrative records upload + metadata
- Strike-off/restore with soft-delete behavior
- Duplicate upload handling: cancel / keep history / overwrite
- Role-based access control for Super Admin / Admin / Encoder
