# Changelog

All notable changes to DOSTorage are documented in this file.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).  
Versioning follows team release tags when cut; until then use `[Unreleased]`.

## [Unreleased]

### Added
- Stitch implementation plan locked from Wakin Q1–Q12 answers (`planning/STITCH_IMPLEMENTATION_PLAN.md`); Hermes comparison + `db-integration` reject note (2026-08-04).
- Agentic / stitch trail changelog process (`planning/AGENTIC_CHANGELOG.md`) institutionalised in CONTRIBUTING + AGENTS.
- `file_groups` taxonomy table + `FileGroup` model; `file_types.file_group_id` / `metadata_template`; `documents.metadata` (BE-03).
- Schema map + Q9 primary-key draft (`docs/db/SCHEMA_MAPPING.md`, `docs/db/METADATA_PRIMARY_KEYS.md`).
- Open PR review snapshot (`planning/PR_REVIEW_OPEN_2026-08-04.md`).

### Changed
- File type seeder expanded to Wakin taxonomy (groups resolved by slug); DatabaseSeeder seeds FileGroup before FileType.
- Upload feature tests no longer set removed `file_types.year`.
- (stitch in progress) Backend-to-mother port on `feat/be-stitch-backend-to-mother` from `master@142b90f`.

### Removed
- Unused `file_types.year` column (metadata carries year where needed).
- DomPDF targeted for removal as part of stitch (do not reintroduce).

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
