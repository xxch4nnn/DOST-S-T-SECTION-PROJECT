# Changelog

All notable changes to DOSTorage are documented in this file.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).  
Versioning follows team release tags when cut; until then use `[Unreleased]`.

## [Unreleased]

### Added
- Stitch implementation plan locked from Wakin Q1–Q12 answers (`planning/STITCH_IMPLEMENTATION_PLAN.md`); Hermes comparison + `db-integration` reject note (2026-08-04).
- Agentic / stitch trail changelog process (`planning/AGENTIC_CHANGELOG.md`) institutionalised in CONTRIBUTING + AGENTS.
- `file_groups` taxonomy table + `FileGroup` model; `file_types.file_group_id` / `metadata_template`; `documents.metadata` (BE-03 / #35).
- Schema map + Q9 primary-key draft (`docs/db/SCHEMA_MAPPING.md`, `docs/db/METADATA_PRIMARY_KEYS.md`).
- Open PR review snapshot (`planning/PR_REVIEW_OPEN_2026-08-04.md`).
- Project Bible audit handoff pack: `planning/HANDOFF_GUIDE.md`, `planning/HANDOFF_GITHUB_ISSUES.md`, `planning/HANDOFF_COVERAGE_MATRIX.md` (2026-08-04).
- Sample PDF fixtures under `database/sample_pdfs/` (PR-B) with relative-path loader helper.
- QA evidence capture path `planning/exports/phpunit_YYYY-MM-DD.txt` for handoff Issue 9.
- Route-level Spatie role/permission gates on scholars, admin-records, audit-logs, add-file, dashboard (#36); policies + `AuthServiceProvider` (FS-07 finally on master).
- Document download authorization via `DocumentPolicy::download` (#37).
- `offline_queue` table + `OfflineQueueItem` model + `offline:replay` scaffold command (#38).
- Feature tests: `RoutePermissionGateTest`, `OfflineQueueScaffoldTest`.
- Draft technical specification + user manual under `docs/` (PM review before client share).
- Bible Keeper execution prompt + DTR backfill notes under `planning/`.
- Version-pinned official tech-stack documentation index (`docs/TECH_STACK_DOCS.md`) + Cursor rule `.cursor/rules/tech-stack-docs.mdc` for agentic coding.

### Security
- Unauthorized roles receive HTTP 403 on gated routes and document downloads (Encoder blocked from audit logs / admin create+edit).

### Changed
- Roles/permissions matrix expanded with scholar + admin-record CRUD permissions; Encoder view-only for admin records.
- File type seeder expanded to Wakin taxonomy (groups resolved by slug); DatabaseSeeder seeds FileGroup before FileType.
- Upload feature tests no longer set removed `file_types.year`.
- Backend-to-mother stitch continues after #35; next slices PR-B+ on feature branches from `master`.
- Changelog policy enforced in CONTRIBUTING.md with review blocker rule for behavior-changing PRs.
- Active task SoT is checklist + `planning/team_*.csv` (not `TASKS_DETECTED_payload.md`).

### Removed
- Unused `file_types.year` column (metadata carries year where needed).
- DomPDF targeted for removal as part of stitch (do not reintroduce).
- `TASKS_DETECTED_payload.md` retired from standup/reporting/Bible sync; kept only as archived reference.

### Fixed
- None yet beyond test alignment for taxonomy.

---

## How to update (required)

When your PR changes **behavior, schema, seeders, public UI, or CI**:

1. Add a bullet under the correct `[Unreleased]` section (`Added` / `Changed` / `Deprecated` / `Removed` / `Fixed` / `Security`).  
2. Prefer user/impact wording (“Scholars upload validates metadata_template”) over file lists.  
3. Link task/PR id when known (`BE-03`, `#NN`).  
4. Agents must **also** append `planning/AGENTIC_CHANGELOG.md` for investigation/port sessions.

PRs that omit an overdue changelog update should be requested to amend before squash-merge.
