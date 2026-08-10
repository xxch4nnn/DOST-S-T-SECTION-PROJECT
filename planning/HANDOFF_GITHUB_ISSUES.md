# GitHub Issues — DOSTorage V1 Audit Register

Copy/paste ready. Labels suggested: `security`, `backend`, `frontend`, `docs`, `qa`.

## Filed on 2026-08-04 (updated late)

| Pack # | GitHub | Owner | Status |
|--------|--------|-------|--------|
| 1 | [#36](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/36) Spatie route permissions | Chan (#52) | ✅ Closed |
| 2 | [#37](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/37) Document download auth | Chan (#52) | ✅ Closed |
| 3 | [#38](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/38) Offline queue | Chan (#52) | ✅ Closed |
| 4 | [#39](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/39) Retire TASKS_DETECTED_payload | @xxch4nnn | ✅ Closed |
| 5 | [#40](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/40) QA responsive evidence | Miguel (handle TBD) | Open |
| 6 | [#41](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/41) Global search | @WakenMac + Rui | Open (also stitch PR-G) |
| 7 | [#42](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/42) Strike-off/restore UX | Rui (handle TBD) | Open |
| 8 | [#43](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/43) Audit-log user-deletion policy | @WakenMac | Open |
| 9 | [#44](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/44) phpunit/CI evidence | @xxch4nnn | ✅ Closed |
| 10 | [#45](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/45) Super Admin seed guarantee | @WakenMac | Open |
| 11 | [#85](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/85) Implement Backend Logic for Dashboard Recent Searches | @WakenMac | Open |

Next team sync: **2026-08-06 10:00 AM** — confirm Rui/Miguel GitHub handles (`Mushimuche`?).

See also stitch backlog issues filed 2026-08-04 late (PR-D/E/F, DomPDF, db-integration) in AGENTIC_CHANGELOG / GitHub.

## Filed on 2026-08-13 (open-PR triage)

| Issue | Owner | Path | Status |
|-------|--------|------|--------|
| [#22](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/22) / PR [#81](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/81) Layout overflow + inline cleanup | `@Mushimuche` | A — UI prereq | ✅ Merged `534fd3d` (2026-08-13) |
| [#93](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/93) Finish PR #86 recent-searches UI blockers (post-#81 rebase) | `@Mushimuche` | A — UI | Open — **next**; rebase onto master after #81 |
| [#94](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/94) Path A merge Dependabot #88–#91 + re-pin TECH_STACK_DOCS | `@xxch4nnn` | B — deps | Open |
| [#95](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/95) Slim `date_issued` + DocumentObserver delta (supersede #92/#87/#68) | `@WakenMac` | C — backend | Open — **do not merge #92/#87/#68** |
| [#96](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/96) Close superseded db-integration / layout hygiene | Chan / Wakin | D — hygiene | Open — #22 closed via #81; still close #68/#87 when #95 lands |

### Blocker note — Wakin `#92` vs queue `#93`

PR [#92](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/92) (`feat/be-56-document-scholar-observers`) **must not be merged**. It still uses pre-rename `ScholarshipProgram` / `ScholarshipProgramType` naming and touches `DocumentController`, `Document`, `Scholar`, `ScholarObserver`, plus `document-viewer` / `file-search` / `scholar-drawer` Blade views.

**File overlap with #93 / PR #86:** `resources/views/livewire/dashboard/file-search.blade.php` (also shared changelog paths). Treat #92 as a **blocker for any #95 cherry-pick into search UI**, and when working #93 only edit the #86 tip rebased on post-#81 `master` — do not pull #92 into that branch.

Cross-links: [#56](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/56), [#58](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/58), [#85](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/85).

Suggested order: ~~merge #81~~ → **#93** → Path B (#94) in parallel → #95/#96 with Wakin.

---

## Issue 1: Enforce Spatie permissions on all protected routes

**Labels:** `security`, `backend`  
**Assignee:** Wakin  
**Priority:** P0

### Description
The Bible contract requires route-level permission enforcement for document and admin flows. Currently `routes/web.php` only uses `auth`/`verified`; no Spatie gates or role middleware are applied.

### Acceptance Criteria
- All document, scholar, admin-record, and audit-log routes require appropriate Spatie permissions.
- Unauthorized users receive 403; unauthorized roles cannot reach controllers/components.
- `permission.php` cache behavior verified after role/permission changes.

### Checklist
- [ ] Add middleware/gate enforcement in `routes/web.php`
- [ ] Verify Super Admin / Admin / Encoder behavior per `SPATIE_ROLES_BASELINE.md`
- [ ] Add/update feature test for forbidden access

---

## Issue 2: Authorize document downloads by role

**Labels:** `security`, `backend`  
**Assignee:** Wakin  
**Priority:** P0

### Description
`DocumentController::download` currently returns files based only on filesystem existence. It does not check whether the authenticated user may download the document.

### Acceptance Criteria
- Encoder/Admin/Super Admin download paths are explicit.
- Unauthorized attempts return 403 before filesystem access.
- Signed-download requirement from checklist is implemented or explicitly deferred with rationale.

### Checklist
- [ ] Add authorization gate to `documents/{document}/download`
- [ ] Add download permission test
- [ ] Confirm 403 behavior manually

---

## Issue 3: Add offline queue / mutation table

**Labels:** `backend`  
**Assignee:** Wakin  
**Priority:** P1

### Description
Checklist §8 expects an offline-first mutation queue. No offline queue migration/model exists in `database/migrations`.

### Acceptance Criteria
- `offline_queue` migration created with required fields.
- Model + queued replay scaffold present.
- Offline path is functional enough for V1 demo.

### Checklist
- [ ] Create migration
- [ ] Create model
- [ ] Add replay job/command stub
- [ ] Seed/manual test mutation replay

---

## Issue 4: Retire deprecated `TASKS_DETECTED_payload.md` from workflows

**Labels:** `docs`  
**Assignee:** Chan  
**Priority:** P1

### Description
`planning/TASKS_DETECTED_payload.md` is deprecated by `bible_keeper.py`. It should not be used for current status.

### Acceptance Criteria
- No active reporting, standup, or Bible sync flow depends on the payload file.
- References in docs/readme/AGENTS.md updated to point to checklist + CSV task lists.

### Checklist
- [ ] Remove/replace references in planning docs
- [ ] Confirm Bible Keeper mode no longer emits task payload
- [ ] Communicate change to team

---

## Issue 5: Add QA evidence for responsive/browser coverage

**Labels:** `qa`  
**Assignee:** Miguel  
**Priority:** P1

### Description
Bible expects QA evidence for responsive and browser coverage. No explicit QA artifact set was found.

### Acceptance Criteria
- Evidence saved under `planning/exports/` or `qa/`.
- Covers login, scholar index, admin create, upload wizard, strike-off flow.

### Checklist
- [ ] Capture screenshots/records
- [ ] Save phpunit output
- [ ] Attach evidence to QA checklist

---

## Issue 6: Implement global search UX and backend action

**Labels:** `frontend`, `backend`  
**Assignee:** Rui / Wakin  
**Priority:** P2

### Description
Bible expects grouped search results by record type and per-tab search. This is not yet evidenced in routes or Livewire components.

### Acceptance Criteria
- Backend search endpoint returns grouped results.
- Frontend shows grouped results with highlighting.
- Per-tab search works inside Scholar 201 / Admin Records.

### Checklist
- [ ] Backend search action
- [ ] Frontend search view/component
- [ ] Validation test for result grouping

---

## Issue 7: Implement strike-off/restore UX

**Labels:** `frontend`  
**Assignee:** Rui  
**Priority:** P2

### Description
Strike-off/restore interactions are expected in Bible but not evidenced in implemented UI.

### Acceptance Criteria
- Admin-only strike-off confirm dialog.
- Restore modal/action available.
- Audit log captures state transitions.

### Checklist
- [ ] Strike-off UI
- [ ] Restore UI
- [ ] Permission check on both actions

---

## Issue 8: Decide audit-log user-deletion policy

**Labels:** `backend`  
**Assignee:** Wakin  
**Priority:** P2

### Description
`audit_logs.user_id` uses `restrictOnDelete`. Bible expects immutable audit history, but this may block user lifecycle cleanup.

### Acceptance Criteria
- Policy documented in backend checklist or README.
- Migration/model behavior matches policy.

### Checklist
- [ ] Document decision
- [ ] Adjust migration if needed

---

## Issue 9: Verify phpunit suite and CI execution evidence

**Labels:** `qa`, `backend`  
**Assignee:** Chan / Wakin  
**Priority:** P2

### Description
Bible baseline expects PHPUnit suites and CI evidence. No test execution evidence was found in the audited local project path.

### Acceptance Criteria
- `phpunit.xml` present and valid.
- `./vendor/bin/phpunit` runs cleanly.
- CI job runs tests and stores artifact.

### Checklist
- [ ] Confirm phpunit config
- [ ] Run tests locally
- [ ] Add CI step if missing

---

## Issue 10: Guarantee default Super Admin seed state

**Labels:** `backend`  
**Assignee:** Wakin  
**Priority:** P2

### Description
`RolesAndPermissionsSeeder` assigns Super Admin only if `test@example.com` exists. If that user is missing, Super Admin role may be unassigned after seeding.

### Acceptance Criteria
- Seeder creates/ensures at least one Super Admin user.
- Seeder is idempotent.

### Checklist
- [ ] Update seeder logic
- [ ] Verify fresh database seed

## Filed 2026-08-04 (late) — stitch backlog

| GitHub | Title | Owner |
|--------|-------|-------|
| [#54](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/54) | PR-D metadata hard-abort | @xxch4nnn |
| [#55](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/55) | PR-E jsPDF + SortableJS | Rui + Chan/Wakin |
| [#56](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/56) | PR-F observers → audit_logs | @WakenMac |
| [#57](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/57) | DomPDF purge confirm | @xxch4nnn |
| [#58](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/58) | Remediate db-integration | @xxch4nnn |
| [#59](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/59) | offline_queue action handlers | @WakenMac |

---

## Issue 11: Implement Backend Logic for Dashboard Recent Searches

**Labels:** `backend`  
**Assignee:** Wakin  
**Priority:** P2

### Description
The frontend UI and Livewire wireframing for the "Recent Searches" dropdown in the Dashboard is complete (see PR `feat/dashboard-search-recent-ui`). Currently, it uses a mocked `$recentSearches` array. We need backend logic to store, fetch, and clear a user's recent searches.

### Acceptance Criteria
- Recent searches are stored in the database or user session/cache.
- The Dashboard search bar correctly fetches and displays the user's recent history on focus.
- The `clearRecentSearch($id)` and `clearAllRecentSearches()` methods correctly wipe the history from storage.

### Checklist
- [ ] Create `search_history` schema or implement cache/session storage
- [ ] Wire up Livewire `$recentSearches` array to real data
- [ ] Wire up delete/clear methods
- [ ] Add backend tests for search history
