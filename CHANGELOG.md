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
- **2026-08-28 16:22:00 +08:00** · **Chan** (`@xxch4nnn` / Antigravity) — AIOps PR diff validation and inference service integration: added `validate-edit-pr` prompt contract and template, `scripts/validate_pr_diff.py` policy checking CLI, 8-case golden evaluation dataset with quality thresholds (`ai/eval/golden.jsonl`), evaluation runner (`scripts/run_eval.py`), inference endpoints (`/health`, `/metrics`, `/predict`, `/v1/validate-pr`), and comprehensive reviewer summary (`planning/REVIEWER_SUMMARY.md`).

### Fixed
- **2026-08-28 00:08:30 +08:00** · **Chan** (`@xxch4nnn` / Antigravity) — Remove leftover `dd($this)` debug call in `App\Livewire\Scholars\Edit` and sanitize empty string form inputs to `null` before model updates, preventing `SQLSTATE[22007]` date format errors on `clearance_date`. Add `clearance_date` and `for_disposal` casts to `Scholar.php`.
- **2026-08-25 22:16:00 +08:00** · **Chan** (`@xxch4nnn` / Antigravity) — Switch `@js($hasQuery)` in `file-search.blade.php` Alpine `x-show` and `:class` directives to reactive `$wire.query.trim() !== ''` so the dropdown menu dynamically appears when typing search queries.
- **2026-08-25 22:05:00 +08:00** · **Chan** (`@xxch4nnn` / Antigravity) — Include `middle_name` key in `searchResults` array mapping in `file-search.blade.php` to prevent `Undefined array key "middle_name"` PHP error during search rendering.
- **2026-08-18 23:35:00 +08:00** · **Waken** (`@WakenMac` / Antigravity) — Fix `morphMany` relationship in `Scholar.php` by passing `'documentable'` prefix instead of `'documentable_type'`, preventing invalid `documentable_type_type` SQL column errors when loading scholar documents.
- **2026-08-18 16:30:00 +08:00** · **Waken** (`@WakenMac` / Antigravity) — Remove redundant `overflow-auto` scrollbar on `.doc-viewer-canvas` in `document-viewer.blade.php` to eliminate the extra scroller in the document viewer overlay.
- **2026-08-13 00:16:00 +08:00** · **Chan** (`@xxch4nnn` / Antigravity) — Use `attributesToArray()` instead of `toArray()` in `DocumentVersionObserver` to prevent loaded Eloquent relations from being serialized and duplicated inside `current_version`. Move AuditLog creation to `DocumentVersionObserver`, restore `.doc-viewer-paper` scrollbars, and implement ETag / 304 HTTP caching in `DocumentController`.
- **2026-08-10 22:18:00 +08:00** · **Chan** (`@xxch4nnn` / Antigravity) — Fix type error in `App\Livewire\Scholars\Files\Edit`: remove redundant typed `$file_types` property and fetch `FileType` collection using `->get()` assigned to `$fileTypes`.

### Changed
### Changed
- **2026-08-13 23:50:00 +08:00** · **Chan** (`@xxch4nnn`) — PR #86 / #93: rebase onto post-#81 master; clear mock recent-search PII; fix Alpine `--has-dropdown` binding; wire recent rows to `selectScholar`; rename clear control to “clear history”.
- **2026-08-12 11:15:00 +08:00** · **Chan** (`@xxch4nnn`) — Fixed layout feedback from PR #81: single scroll container (`.main-canvas` `overflow: hidden`, `<main>` scroll), reverted silent background shift (`#f4f6fa`), and extracted remaining layout inlines to utility classes.
- **2026-08-10 15:05:00 +08:00** · **Rui** (`@Mushimuche`) — Dashboard search UI refactor: fixed visual jump on dropdown expansion by stabilizing wrapper padding/borders, added Alpine.js fade animations, and Recent Searches UI (see PR #86 / issue #93).
- **2026-08-10 04:45:00 +08:00** · **Chan** (`@xxch4nnn` / Composer) — Dashboard search (#76 / Q09=A): match scholars via `document_versions` filename/type plus mother scholar fields; drop fake demo fallback.
- **2026-08-10 04:35:00 +08:00** · **Chan** (`@xxch4nnn` / Composer) — ScholarObserver (#75 / Q08=C): audit create/update/delete onto `audit_logs.record_*` (no `loggable_*`, no FTS column, no DocumentObserver).
- **2026-08-10 04:20:00 +08:00** · **Chan** (`@xxch4nnn` / Composer) — Document viewer (#74): port pdf.js viewer/print/download/zoom from `db-integration` onto `currentVersion` + `documents.view` stream route (no DomPDF; no folders).
- **2026-08-10 03:55:00 +08:00** · **Chan** (`@xxch4nnn` / Composer) — UUID documents schema (#73): additive migrations reshape `documents` to thin shell (`uuid` dual-key kept with bigint `id`) and move file payload to `document_versions` (`file_path` relative, `file_size_bytes` bigint); upload/download callers + tests updated.

### Added
- **2026-08-10 03:01:00 +08:00** · **Chan** (`@xxch4nnn`) — UUID Documents RFC (`docs/db/DOCUMENTS_UUID_RFC.md`): additive migration plan for thin UUID `documents` shell + file data on `document_versions`, per CHAN_ACK Q05=B.
- **2026-08-10 03:01:00 +08:00** · **Chan** (`@xxch4nnn`) — CHAN_ACK Q02=B, Q05=B, Q07=C on Wakin Q01–Q16 answers lock; all blockers resolved, automation unblocked for thin slice PRs.

### Changed
- **2026-08-10 03:01:00 +08:00** · **Chan** (`@xxch4nnn`) — Folders RFC (`planning/RFC_Q05_FOLDERS_AS_DOCUMENTABLE.md`) status: Open → **Decided: Park for post-V1** (Q07=C).
- **2026-08-10 03:01:00 +08:00** · **Chan** (`@xxch4nnn`) — Wakin answers lock (`planning/WAKIN_Q01_Q16_ANSWERS_LOCK_2026-08-10.md`): Q02/Q05/Q09 status updated from BLOCKED to ACK'd; execution order updated.

### Fixed
- **2026-08-12 16:10:00 +08:00** · **Chan** (`@xxch4nnn` / Antigravity) — Implement ETag / 304 Not Modified HTTP caching in `DocumentController::viewFile`: generate version-based ETag headers, validate `If-None-Match` for instant zero-bandwidth browser re-use when file is unchanged, and automatically invalidate cache whenever a new version is created. Raise memory limit to 512M for PDF binary processing, unify `$relativePath`, and simplify `currentVersion()` to `latestOfMany('id')`.
- **2026-08-10 22:18:00 +08:00** · **Chan** (`@xxch4nnn` / Antigravity) — Fix type error in `App\Livewire\Scholars\Files\Edit`: remove redundant typed `$file_types` property and fetch `FileType` collection using `->get()` assigned to `$fileTypes`.

### Changed
- **2026-08-10 03:55:00 +08:00** · **Chan** (`@xxch4nnn` / Composer) — UUID documents schema (#73): additive migrations reshape `documents` to thin shell (`uuid` dual-key kept with bigint `id`) and move file payload to `document_versions` (`file_path` relative, `file_size_bytes` bigint); upload/download callers + tests updated.

### Added
- **2026-08-10 03:01:00 +08:00** · **Chan** (`@xxch4nnn`) — UUID Documents RFC (`docs/db/DOCUMENTS_UUID_RFC.md`): additive migration plan for thin UUID `documents` shell + file data on `document_versions`, per CHAN_ACK Q05=B.
- **2026-08-10 03:01:00 +08:00** · **Chan** (`@xxch4nnn`) — CHAN_ACK Q02=B, Q05=B, Q07=C on Wakin Q01–Q16 answers lock; all blockers resolved, automation unblocked for thin slice PRs.

### Changed
- **2026-08-10 03:01:00 +08:00** · **Chan** (`@xxch4nnn`) — Folders RFC (`planning/RFC_Q05_FOLDERS_AS_DOCUMENTABLE.md`) status: Open → **Decided: Park for post-V1** (Q07=C).
- **2026-08-10 03:01:00 +08:00** · **Chan** (`@xxch4nnn`) — Wakin answers lock (`planning/WAKIN_Q01_Q16_ANSWERS_LOCK_2026-08-10.md`): Q02/Q05/Q09 status updated from BLOCKED to ACK'd; execution order updated.

### Added
- **2026-08-19 13:54:00 +08:00** · **Palab** (`@palab`) — Added Alpine listener for `document-updated` event and session flash support in `notification-toast.blade.php` to handle Waken's DocumentObserver backend events (PR #92).
- **2026-08-19 13:54:00 +08:00** · **Palab** (`@palab`) — Added Alpine listener for `document-updated` event and session flash support in `notification-toast.blade.php` to handle Waken's DocumentObserver backend events (PR #92).
- **2026-08-08 16:56:00 +08:00** · **Chan** (`@xxch4nnn`) — EC2 staging sandbox runbook + `scripts/deploy-staging.sh` (single-box Nginx/PHP/MySQL path; not production Decision Gate) (`planning/AWS_STAGING_EC2_RUNBOOK.md`).
- **2026-08-08 16:56:00 +08:00** · **Chan** (`@xxch4nnn`) — EC2 staging sandbox runbook + `scripts/deploy-staging.sh` (single-box Nginx/PHP/MySQL path; not production Decision Gate) (`planning/AWS_STAGING_EC2_RUNBOOK.md`).
- **2026-08-08 13:50:00 +08:00** · **Chan** (`@xxch4nnn`) — Scholar upload/edit persist `documents.metadata.category` alongside `document_versions` on staged saves (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Scholar file upload wizard, edit-scholar document management, notifications center + corner toasts (#65 / `@Mushimuche`).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — `viewNotifications` permission in `RolesAndPermissionsSeeder`, assigned to Super Admin, Admin, and Encoder roles (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Document versioning (`DocumentVersion`) creation for staged file uploads in `AddFile` and `Scholars/Edit` (#65).
- **2026-08-06 01:45:00 +08:00** · **Chan** (`@xxch4nnn`) — Wakin Q1–Q12 vs mother implementation matrix + DB adoption guide (`planning/WAKIN_Q12_VS_IMPLEMENTATION_2026-08-06.md`); PR #65 cross-check addendum (`planning/PR65_REVIEW_2026-08-06.md`).
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
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Gated `notifications.index` route behind `permission:viewNotifications` and verified email/auth (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Authorized document deletion in `Scholars/Edit` with `DocumentPolicy::delete` check and scoped composite `documentable_type` + `documentable_id` filtering against cross-morph deletion (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Gated `notifications.index` route behind `permission:viewNotifications` and verified email/auth (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Authorized document deletion in `Scholars/Edit` with `DocumentPolicy::delete` check and scoped composite `documentable_type` + `documentable_id` filtering against cross-morph deletion (#65).
- **2026-08-04 15:42:00 +08:00** · **Chan** (`@xxch4nnn`) — Unauthorized roles receive HTTP 403 on gated routes and document downloads (Encoder blocked from audit logs / admin create+edit) (#52).

### Changed
- **2026-08-05 13:38:00 +08:00** · **Chan** (`@xxch4nnn`) — Update `database/migrations/2026_07_20_061543_create_documents_table.php` schema definition to use `documents` table name, polymorphic `documentable_type`/`documentable_id` columns with compound index, timestamps, and soft deletes.
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — `Notifications/Index` component refactored to query real unread `AuditLog` records instead of hardcoded session mocks (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Livewire pagination theme configured to `bootstrap` in `config/livewire.php` (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — `Notifications/Index` component refactored to query real unread `AuditLog` records instead of hardcoded session mocks (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Livewire pagination theme configured to `bootstrap` in `config/livewire.php` (#65).
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
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Removed empty Volt stub `resources/views/components/scholars/files/⚡edit.blade.php` and disabled mock `scholars.files.edit` route (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Eliminated placeholder fallback byte generation and `auth()->id() ?? 1` fallbacks in `AddFile` and `Scholars/Edit` (#65).
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Removed invalid `FileType::where('is_available')` and `year` query constraints (#65).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Unused `file_types.year` column (metadata carries year where needed) (#35).
- **2026-08-04 01:30:00 +08:00** · **Chan** (`@xxch4nnn`) — DomPDF targeted for removal as part of stitch (do not reintroduce).
- **2026-08-04 02:20:00 +08:00** · **Chan** (`@xxch4nnn`) — `TASKS_DETECTED_payload.md` retired from standup/reporting/Bible sync; archived reference only (#46 / #39).

### Fixed
- **2026-08-06 15:20:00 +08:00** · **Chan** (`@xxch4nnn`) — Enforced strict `Scholar::findOrFail` in `Scholars/Edit` mount to 404 instead of falling back to Wakin mock profile (#65).
- **2026-08-08 13:50:00 +08:00** · **Chan** (`@xxch4nnn`) — Removed mock scholar list injection from `Scholars/Index`; empty DB shows empty groups (#65).
- **2026-08-08 13:50:00 +08:00** · **Chan** (`@xxch4nnn`) — `Scholars/Edit` staged save uses manifest **or** temp categories (not both) to prevent duplicate documents (#65).
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
