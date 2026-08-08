# Changelog

All notable changes to DOSTorage are documented in this file.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), extended with **date-time** and **user** on every bullet.  
Versioning follows team release tags when cut; until then use `[Unreleased]`.

## Entry format (required)

Every bullet **must** start with:

```text
- **YYYY-MM-DD HH:mm:ss +08:00** · **Name** (`@github` or `git:user <email>`) — summary
```

- **Timezone:** Asia/Manila (`+08:00`) unless the author is working in another zone (then use that zone’s offset and say so).
- **User:** human owner of the change (GitHub login and/or `git config user.name` / `user.email`). Agents list the **human principal** first (e.g. Chan), then agent label if useful (`Chan` / Composer).
- Do not omit time; if only the calendar day is known for a backfill, use `12:00:00 +08:00` and note `(time approximate)`.

## [Unreleased]

### Added
- **2026-08-05 00:40:00 +08:00** · **Chan** (`@xxch4nnn`) — AWS production deployment design prompt (planning SoT only; Decision Gate before provision) (`planning/AWS_PRODUCTION_DEPLOYMENT_PROMPT.md`).
- **2026-08-05 00:40:00 +08:00** · **Chan** (`@xxch4nnn`) — CI Path 3: `synthetic-smoke` job (PHPUnit `@group smoke` + live `/health`/`/login` curl) and gated manual `deploy.yml` (staging/prod; requires `confirm_aws_gates=APPROVED`).
- **2026-08-05 00:40:00 +08:00** · **Chan** (`@xxch4nnn`) — `ProductionSmokeTest` covering health, auth, upload, download forbid/allow (`tests/Feature/ProductionSmokeTest.php`).
- **2026-08-05 00:13:37 +08:00** · **Chan** (`@xxch4nnn` / `xxch4nnn <johned623@gmail.com>`) — Changelog bullets now require date-time + user (this policy); see “How to update”.
- **2026-08-05 00:11:00 +08:00** · **Chan** (`@xxch4nnn`) — Version-pinned official tech-stack documentation index (`docs/TECH_STACK_DOCS.md`) + Cursor rule `.cursor/rules/tech-stack-docs.mdc` for agentic coding (#61).
- **2026-08-04 16:00:00 +08:00** · **Chan** (`@xxch4nnn`) — Draft technical specification + user manual under `docs/` (PM review before client share) (#53).
- **2026-08-04 16:00:00 +08:00** · **Chan** (`@xxch4nnn`) — Bible Keeper execution prompt + DTR backfill notes under `planning/` (#53).
- **2026-08-04 15:42:00 +08:00** · **Chan** (`@xxch4nnn`) — Route-level Spatie role/permission gates; policies + `AuthServiceProvider`; download 403; `offline_queue` scaffold (#52 / #36–#38).
- **2026-08-04 15:42:00 +08:00** · **Chan** (`@xxch4nnn`) — Feature tests: `RoutePermissionGateTest`, `OfflineQueueScaffoldTest` (#52).
- **2026-08-04 02:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Sample PDF fixtures under `database/sample_pdfs/` (PR-B) with relative-path loader helper (#46).
- **2026-08-04 02:20:00 +08:00** · **Chan** (`@xxch4nnn`) — QA evidence capture path `planning/exports/phpunit_YYYY-MM-DD.txt` (#46 / #44).
- **2026-08-04 02:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Project Bible audit handoff pack: `HANDOFF_GUIDE.md`, `HANDOFF_GITHUB_ISSUES.md`, `HANDOFF_COVERAGE_MATRIX.md` (#46).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — `file_groups` taxonomy + `metadata_template` / `documents.metadata` (BE-03 / #35).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Schema map + Q9 primary-key draft (`docs/db/SCHEMA_MAPPING.md`, `docs/db/METADATA_PRIMARY_KEYS.md`) (#35).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Stitch implementation plan locked from Wakin Q1–Q12; Hermes comparison + `db-integration` reject note (#35).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Agentic / stitch trail changelog process institutionalised in CONTRIBUTING + AGENTS (#35).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Open PR review snapshot (`planning/PR_REVIEW_OPEN_2026-08-04.md`) (#35).

### Security
- **2026-08-04 15:42:00 +08:00** · **Chan** (`@xxch4nnn`) — Unauthorized roles receive HTTP 403 on gated routes and document downloads (Encoder blocked from audit logs / admin create+edit) (#52).

### Changed
- **2026-08-05 13:38:00 +08:00** · **Chan** (`@xxch4nnn`) — Update `database/migrations/2026_07_20_061543_create_documents_table.php` schema definition to use `documents` table name, polymorphic `documentable_type`/`documentable_id` columns with compound index, timestamps, and soft deletes.
- **2026-08-05 00:30:00 +08:00** · **Chan** (`@xxch4nnn`) — `AGENTS.md` / `CONTRIBUTING.md`: migrated Bible doc ID, Dependabot Paths A/B/C, CODEOWNER hold-release, Windows shell matrix, handoff ownership, changelog examples; re-pin concurrently **10.0.4** in `docs/TECH_STACK_DOCS.md` after #31 smoke.
- **2026-08-05 00:27:19 +08:00** · **Chan** (`@xxch4nnn`) — Bump `concurrently` 9.2.4 → 10.0.4 (Dependabot #31; local smoke Path A/C).
- **2026-08-05 00:13:37 +08:00** · **Chan** (`@xxch4nnn`) — Changelog policy: every `[Unreleased]` bullet must include date-time (+08:00) and user identity.
- **2026-08-04 15:42:00 +08:00** · **Chan** (`@xxch4nnn`) — Roles/permissions matrix expanded with scholar + admin-record CRUD; Encoder view-only for admin records (#52).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — File type seeder expanded to Wakin taxonomy; DatabaseSeeder seeds FileGroup before FileType (#35).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Upload feature tests no longer set removed `file_types.year` (#35).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Backend-to-mother stitch continues after #35; next slices on feature branches from `master`.
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Changelog policy enforced in CONTRIBUTING.md with review blocker for behavior-changing PRs.
- **2026-08-04 02:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Active task SoT is checklist + `planning/team_*.csv` (not `TASKS_DETECTED_payload.md`).

### Removed
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Unused `file_types.year` column (metadata carries year where needed) (#35).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — DomPDF targeted for removal as part of stitch (do not reintroduce).
- **2026-08-04 02:20:00 +08:00** · **Chan** (`@xxch4nnn`) — `TASKS_DETECTED_payload.md` retired from standup/reporting/Bible sync; archived reference only (#46 / #39).

### Fixed
- **2026-08-05 12:24:00 +08:00** · **Chan** (`@xxch4nnn`) — Remove outer scrollbar from `.doc-viewer-canvas` container in `_document-viewer.scss` and `document-viewer.blade.php` so only the internal document viewer scrollbar is visible.
- **2026-08-05 12:12:00 +08:00** · **Chan** (`@xxch4nnn`) — Fix 404 Not Found error in `DocumentController` `viewFile` and `download` actions by correctly resolving `$file->file_path` against local storage disk and filesystem paths.
- **2026-08-05 12:10:00 +08:00** · **Chan** (`@xxch4nnn`) — Add missing closing `</div>` tag for `.doc-thumbnail-card` inside `@foreach` loop in `resources/views/livewire/dashboard/scholar-drawer.blade.php` to prevent card nesting and vertical layout overflow.
- **2026-08-05 12:00:00 +08:00** · **Chan** (`@xxch4nnn`) — Fix inline JS Blade single-quote parsing issue in dropzone element by using dataset attribute `data-cat-id` in `resources/views/livewire/add-file.blade.php`.
- **2026-08-05 11:57:00 +08:00** · **Chan** (`@xxch4nnn`) — Update `UserSeeder`, `DatabaseSeeder`, and `RolesAndPermissionsSeeder` to assign Spatie roles (`Super Admin` / `Admin`) to seeded user accounts so login permissions pass.
- **2026-08-05 11:42:25 +08:00** · **Chan** (`@xxch4nnn`) — Replace `@this` with `$wire` in Blade JavaScript blocks to resolve IDE "Decorators are not valid here" syntax errors in `add-file.blade.php`, `scholars/edit.blade.php`, and `scholars/files/edit.blade.php`.
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Test alignment for taxonomy / FileType without `year` (#35).

---

## How to update (required)

When your PR changes **behavior, schema, seeders, public UI, or CI**:

1. Add a bullet under the correct `[Unreleased]` section (`Added` / `Changed` / `Deprecated` / `Removed` / `Fixed` / `Security`).  
2. Prefix with **date-time** (`YYYY-MM-DD HH:mm:ss +08:00`) and **user** (`Name` + `@github` and/or git identity).  
3. Prefer user/impact wording (“Scholars upload validates metadata_template”) over file lists.  
4. Link task/PR id when known (`BE-03`, `#NN`).  
5. Agents must **also** append `planning/AGENTIC_CHANGELOG.md` with Date, Time, User, Actor (if agent), Repo@branch, Action, Commit/PR, Summary, Linked.

PRs that omit date-time, user, or an overdue changelog update should be requested to amend before squash-merge.
