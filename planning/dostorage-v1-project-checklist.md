# DOSTorage V1 — Exhaustive Project-Wide Feature Checklist

> Stack: Laravel / Livewire / Spatie / Docker / Bootstrap 5 / MySQL
> Scope: Scholar 201 + Administrative Records only. Financial Ledger = future.
> Roles: Chan (Fullstack / AIOps), Miguel (PM / UI-UX), Rui (Frontend), Wakin (Backend & DB + QA)
> Hour Budget: 162 hours per member; 648 total shared pool

---

## How to Use This Checklist
- Hierarchical structure: **Epic → Feature → Sub-feature → Task**
- Every item maps to: **Owner | Hours | Dependencies | Acceptance Criteria | Test Approach**
- Hour totals are aligned to the 0–162 per-member schedule.
- Do not exceed member budgets without explicit scope trade-off.

---

## Legend / Acronyms
- **FS** = Chan (Fullstack / AIOps)
- **PM** = Miguel (PM / UI-UX)
- **FE** = Rui (Frontend)
- **BD** = Wakin (Backend & DB + QA)
- **Xref** = dependency marker, e.g., `FD-01` means “depends on Feature/Task FD-01”

---

# Epic 1 — Infrastructure & DevOps
**Owner:** FS | **Total Hours:** 73

## Feature 1.1 — Local Development Environment
**Owner:** FS | **Hours:** 24 | **Dependencies:** None

### Sub-feature 1.1.1 — Repository Scaffolding
**Owner:** FS | **Hours:** 6 | **Dependencies:** None
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Initialize Laravel 11 project with Breeze starter kit | 3 | None | `laravel new` succeeds; Breeze auth scaffold present | `composer install` + `php artisan serve` returns login page |
| Configure Vite + Tailwind + Bootstrap 5 coexistence | 2 | None | Tailwind utility classes render alongside Bootstrap components without layout breakage | Visual smoke test of home + login pages |
| Create `planning/` artifact directory and baseline docs | 1 | None | Directory exists with Gantt, checklist, burndown placeholders | `ls planning/` returns expected files |

### Sub-feature 1.1.2 — Docker Runtime
**Owner:** FS | **Hours:** 14 | **Dependencies:** FS-Repo Scaffolding
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Author `Dockerfile` optimized for Laravel 11 (PHP 8.3) | 5 | None | Build succeeds; web root serves Laravel | `docker build` + `docker run` smoke test |
| Author `docker-compose.yml` (app + mysql + optional phpmyadmin) | 5 | None | `docker compose up -d` brings up healthy containers | `docker compose ps` shows all healthy; DB connection succeeds |
| Volume mounts for uploads/storage and MySQL initdb | 2 | None | Persistent storage survives container recreation | Write file to mounted volume, restart container, file persists |
| `.dockerignore`, healthcheck endpoint, and env defaults | 2 | None | Healthcheck reports `healthy`; `.dockerignore` reduces context size | `docker inspect --format='{{.State.Health.Status}}' app` returns `healthy` |

### Sub-feature 1.1.3 — Backup / Restore Runbook
**Owner:** FS | **Hours:** 4 | **Dependencies:** Docker Runtime
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Write `scripts/backup.sh` for MySQL + storage volume | 2 | None | Creates timestamped `backups/` archive with SQL dump + files | Run script; inspect archive contents |
| Write `scripts/restore.sh` for DB + files restoration | 2 | None | Restores to point-in-time state from archive | Backup → mutate DB → restore → verify data matches |

## Feature 1.2 — CI/CD & Automation
**Owner:** FS | **Hours:** 22 | **Dependencies:** Docker Runtime
### Sub-feature 1.2.1 — GitHub Actions Pipeline
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Scaffold `.github/workflows/ci.yml` lint + test job | 4 | None | `push` to default branch triggers green pipeline | Inspect Actions run logs |
| Add `artisan test` step with MySQL service container | 4 | None | Tests pass using ephemeral DB | Pipeline artifacts include test report |
| Build Docker image in CI and cache layers | 4 | None | Image build completes within cache improvement threshold | CI timing comparison before/after cache |

### Sub-feature 1.2.2 — Bible Keeper Automation
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Maintain `scripts/bible_keeper.bat` cron wrapper | 4 | None | Script runs 12:00 PM and 7:00 PM without manual intervention | Windows Task Scheduler / cron dry run |
| Conflict tagging + diff trim logic | 4 | None | Conflicting Open Floor items are flagged with `[CONFLICT]` | Run against fixture conflicts; inspect output |
| Archive resolved items workflow | 2 | None | Resolved items moved to Archived section; no duplicate Open entries | Post-run Bible file inspection |

### Sub-feature 1.2.3 — LAN Readiness & Offline Checklist
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Document LAN access procedures and firewall guidance | 2 | None | Runbook details port-forwarding and IP access | Q&A review by team |
| Smoke test offline connectivity inside isolated network | 2 | Docker Runtime | App loads without external DNS/HTTP calls | Disable external NIC in VM/local test; verify app remains reachable |

---

# Epic 2 — Auth, Roles & Permissions
**Owner:** BD | **Total Hours:** 37 | **Dependencies:** FS-Docker Runtime

## Feature 2.1 — User & Role Foundation
**Sub-feature 2.1.1 — Spatie Laravel-Permission Setup**
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Install and configure `spatie/laravel-permission` | 2 | FS-Docker Runtime | Package registered; migrations run | `php artisan migrate` succeeds |
| Seed roles: Super Admin, Admin, Encoder | 1 | None | `Role::all()` returns three seeded roles | Tinker / seed verification |
| Seed permissions for each role per matrix | 2 | None | Permissions present for upload, edit, delete/archive, approve, reports, manage users | Tinker check |
| Attach permissions to roles; verify gates | 2 | None | Encoder lacks Manage Users; Super Admin has all | Feature test per role |

### Sub-feature 2.1.2 — User Management UI/Backend
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: User CRUD controllers + FormRequests | 3 | Spatie Setup | Admin/Super Admin can create users; Encoder cannot | Feature tests |
| Frontend: User management Livewire page | 3 | User CRUD controllers | Page lists users, roles, permission checkboxes | Livewire test: assert role assignment reflected |
| Auth middleware per role on routes | 2 | None | Unauthorized role access redirects to 403 | Pest feature test per route |

### Sub-feature 2.1.3 — Login & Audit Trail Integration
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Login audit log entry + metadata capture | 2 | None | Successful login writes to `audit_logs` with timestamp + IP | Feature test: log count increases |
| Logout + session timeout behavior | 2 | None | Logout clears session; idle timeout redirects to login | Browser session test |
| Password policy enforcement (min length + hashing) | 2 | None | Weak password rejected; DB stores hash only | FormRequest test |

## Feature 2.2 — Role Behaviors & Gates
**Owner:** BD | **Hours:** 16 | **Dependencies:** User & Role Foundation
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Gate: Upload / Edit Metadata / Archive / Approve / Reports / Manage Users | 4 | User Management UI/Backend | Each role sees only permitted actions | Policy test matrix per role |
| Supervisor/auditor extensibility registry schema | 4 | None | `roles` table remains data-driven; no hardcoded role count | Schema review |
| Concurrent edit indicator backend support | 6 | Gate matrix | Two active sessions for same record do not conflict with DB constraints | Integration test: parallel edit simulation |
| Permission cache invalidation on role update | 2 | User CRUD | Cache cleared when role/permission changes | Assert cache miss after mutation |

## Feature 2.3 — Security Hardening
**Owner:** FS | **Hours:** 5 | **Dependencies:** Docker Runtime
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Enforce HTTPS in production Docker profile | 1 | None | App behind Nginx terminates TLS; HTTP redirects | Docker compose with reverse proxy test |
| Secure Laravel session + cookie config | 1 | None | `SESSION_SECURE_COOKIE=true` in production env | Inspect `.env` + artisan config |
| Rate limiting auth endpoints | 1 | None | 5 failed attempts locks for 60 seconds | Automated script + assertion |
| Environment secrets handling + .env leak prevention | 2 | None | `.env` excluded from repo; `phpunit` without secrets passes | `grep -r secret` test |

---

# Epic 3 — Data Model & Migrations
**Owner:** BD | **Total Hours:** 40 | **Dependencies:** FS-Docker Runtime

## Feature 3.1 — Lookup & Supporting Tables
### Sub-feature 3.1.1 — Lookups
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Roles/permissions tables via Spatie | 1 | Spatie Setup | Migrations run clean | `php artisan migrate:fresh` |
| Regions, Municipalities, Schools, Courses tables | 4 | None | Dropdowns seed regional DOST data | Seed command output inspected |
| Document Types / File Types lookup tables | 4 | None | Scholar 201 + Admin Records types listed | Seed data count + API |
| Record Status, Clearance Status lookup tables | 2 | None | `status` field constrained to lookup values | Validation test |

### Sub-feature 3.1.2 — Soft-Delete, Strike-Off, Archive Columns
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Enable `softDeletes` on scholars/administrative_records/documents | 2 | None | `deleted_at` populated without removing row | Unit test soft delete |
| Add `strike_off_status` + `restored_at` columns | 2 | None | Strike-off sets status; restore nullifies deleted_at | Model state transition test |
| Add `archived_at` + archive lifecycle helper methods | 2 | None | Record marked `archived_at` after 5 or 10 years | Artisan command / job test |

## Feature 3.2 — Core Records Tables
### Sub-feature 3.2.1 — Scholars Table
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| `scholars` migration with required fields + lookups | 6 | Lookup tables | Migration rolls back cleanly | `php artisan migrate:rollback` round trip |
| Indexes: composite `spas_no + document_type + date_issued` | 2 | None | Composite index present in `information_schema` | Explain plan test |
| Scholar model + relationships (documents, administrative_records) | 3 | None | `scholar->documents` eager loads correctly | PHPUnit model test |
| Scholar seeder with realistic edge cases (pre-2000) | 2 | None | Pre-2000 duplicate SPAS records seed without error | Seed count + constraint check |

### Sub-feature 3.2.2 — Administrative Records Table
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| `administrative_records` migration with nullable `scholar_id` | 5 | Lookups | Standalone admin record rows allowed | Insert null scholar_id + assert no FK violation |
| Indexes on `scholar_id`, `issued_at`, `record_type` | 2 | None | Indexes visible and optimize filters | Explain plan test |

### Sub-feature 3.2.3 — Scholar History / Versioned Metadata Table
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| `scholar_history` table for transfer/change history | 5 | Scholars Table | Updates to scholar create immutable history row | Integration test: update field, history row created |

## Feature 3.3 — Document Storage & Versioning Tables
### Sub-feature 3.3.1 — Documents Table
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| `documents` migration with polymorphic owner or explicit FKs | 6 | Scholars / Admin Records | One document belongs to scholar or admin record | Model attach test |
| Columns: path, mime, original_filename, size, uploaded_by, strike/archive | 3 | None | All columns present and populated on upload | Feature test upload flow |

### Sub-feature 3.3.2 — Document Versions Table
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| `document_versions` migration | 3 | Documents | Uploading new version increments count | Versioning job test |
| Version metadata JSON column | 1 | None | Stores OCR status, page count, checksum | Insert + assert JSON correct |

### Sub-feature 3.3.3 — Merge/Duplicate Tables
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| `merges` + `merge_items` migrations | 4 | None | Merge proposal records field-level resolutions | Factory + merge job test |

### Sub-feature 3.3.4 — Audit Logs Table
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| `audit_logs` migration with JSON metadata | 3 | None | Logs are append-only; deletions from log fail | Integrity test |
| Observer/dedicated job writes audit entries | 4 | None | CRUD actions mutate log entries | Feature test count assertions |

---

# Epic 4 — Scholar 201 Records
**Owner:** FE + BD | **Total Hours:** 36 | **Dependencies:** BD-Core Tables, FS-Docker Runtime

## Feature 4.1 — Scholar CRUD Interface
### Sub-feature 4.1.1 — List Page (Index)
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: Livewire ScholarIndex component | 4 | BD-Scholars Table | Paginated list with sortable columns | Pest/Livewire assert records visible |
| Frontend: Bootstrap table + filters Livewire | 4 | Backend-ScholarIndex | Filters by name, SPAS, award year work client-side or server-side | Livewire test filter + reload |
| Search integration within Scholar 201 tab | 4 | Search backend | Debounced search returns matching scholars | Automation test assert results |

### Sub-feature 4.1.2 — Create Scholar Record
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: Create Scholar FormRequest validation | 2 | None | Validation fails on missing required fields | Pest invalid input test |
| Frontend: Scholar create form layout | 3 | None | All required fields present with Bootstrap validation styles | Visual QA |
| Backend: Store action with redirect + success flash | 2 | None | Redirects to show page after creation | Feature test |

### Sub-feature 4.1.3 — Scholar Show / Detail Page
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Frontend: Show page with metadata + document list tab | 5 | Create Scholar Record | Page shows scholar info and linked documents tab | Livewire test presence assertions |
| Backend: attach document action via relationship | 3 | Documents backend | Document appears on show page after upload | Full stack test |
| Offline schema: form is submit-ready when network restores | 3 | Offline queue | Queue replay succeeds after reconnect | Integration test offline simulation |

### Sub-feature 4.1.4 — Edit / Update Scholar
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: Update FormRequest + `scholar_history` observer | 3 | Scholar history table | Edited fields create history rows; updates reflect | Feature test assert history |
| Frontend: Edit form with preloaded values | 3 | Create Scholar Record | User can update address/course without overwriting history | Livewire test field values |

## Feature 4.2 — Scholar State Management
### Sub-feature 4.2.1 — Strike-Off Flow
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: strike-off action + status enum | 2 | None | Record marked struck; soft-deleted with flag | Feature test state change |
| Frontend: Strike-off button with Encoder notice | 1 | None | Encoder sees button but must confirm; Admin sees direct action | Livewire test button visibility per role |
| Audit trail: strike-off event logged | 1 | None | Audit log contains action + user | Assert audit entry exists |

### Sub-feature 4.2.2 — Restore Flow
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: restore action clears deleted_at and strike flag | 2 | Strike-off flow | Restored record reappears in index | Feature test |
| Frontend: Restore modal in index and show views | 2 | Backend restore action | Modal triggers Livewire action with success feedback | Livewire test |

## Feature 4.3 — Offline-First Behavior
### Sub-feature 4.3.1 — Offline Queue / Outbox
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: outbox table + replay command | 5 | BD-Docker Runtime | Pending jobs retry on scheduler run | Feature test |
| Frontend: queue UI indicator + sync badge | 3 | Backend outbox | Indicator shows pending/synced state | Manual/automation UI test |

---

# Epic 5 — Administrative Records
**Owner:** FE + BD | **Total Hours:** 24 | **Dependencies:** BD-Core Tables, FS-Docker Runtime

## Feature 5.1 — Administrative Records CRUD
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: AdminRecordController + FormRequest (nullable scholar_id) | 4 | BD-Admin Records Table | Create succeeds with or without scholar | Feature test |
| Frontend: Admin record index + create/show/edit views | 5 | BD-Admin Records Table | Standalone records list without scholar filters | Livewire test |
| Document association to admin record | 3 | BD-Document mutations | `/api/admin-records/{id}/documents` returns correct set | Factory + API test |

## Feature 5.2 — Standalone Administrative Files
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: Allow null `scholar_id`; no orphan deletion | 2 | BD-Admin Records Table | Soft-delete scholar does not cascade delete admin record | Integration test |
| Frontend: “Link to Scholar” modal | 3 | Backend standalone files | Admin record can be linked post-creation | Livewire test |

## Feature 5.3 — Archive Lifecycle (10 Years)
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: Archive feature flag + scheduled command | 3 | BD-Archive columns | Command sets `archived_at` after 10 years | Command output test |
| Frontend: Archive badge on record list + detail | 2 | Backend archive command | Records past 10 years display archive badge | Seed archive-old + UI assertion |

---

# Epic 6 — Document Storage & Versioning
**Owner:** BD + FE | **Total Hours:** 53 | **Dependencies:** FS-Docker Runtime

## Feature 6.1 — Upload Pipeline
### Sub-feature 6.1.1 — Backend Upload Core
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Upload controller + FormRequest (mime + size + metadata) | 3 | BD-Documents Table | Rejects >10 MB; accepts PDF and image mimes | Pest feature test |
| Filesystem config with private disk + UUID paths | 3 | Docker runtime volumes | Files stored under `storage/app/private/uploads/` with UUID names | Assert path + existence |
| Store and wire `uploaded_by` user FK | 1 | None | DB record links to uploader | Insert + assert |
| Virus/basic MIME verification middleware | 2 | Upload backend | Fake SVG/EXE upload blocked by allowed list | Negative test cases |

### Sub-feature 6.1.2 — Frontend Upload UI
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Drag-and-drop upload zone + file picker | 4 | Upload JS plan | Drag-drop triggers upload; picker fallback works | Livewire + Alpine test |
| 10 MB size validation + client preview | 2 | Drag-drop zone | Oversize file blocked before upload | Negative test |
| Multiple-file upload queue with progress | 4 | Drag-drop zone | Queue shows per-file progress + success/fail | Manual + automation |

### Sub-feature 6.1.3 — Validation & Error UX
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend error response shape with field-level detail | 2 | Upload backend | UI renders exact validation message | API contract test |
| Frontend inline validation messages | 2 | Error response shape | Banner + field-level errors render under inputs | Livewire test assert message presence |

## Feature 6.2 — Document Versioning
### Sub-feature 6.2.1 — Versioning Logic
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: append version logic with sequence | 3 | BD-Document Versions | New upload increments version | Feature test version count |
| Frontend: Version selector dropdown in document section | 3 | Versioning logic | Dropdown lists all versions and dates | Livewire test assert options |

### Sub-feature 6.2.2 — Replacement / Keep History / Overwrite Flow
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Duplicate detection backend (same name + same scholar/admin) | 2 | Upload core | Detects potential duplicates and emits event | Feature test |
| Frontend duplicate decision modal | 4 | Detection backend | Modal offers Cancel/Keep History/Overwrite | Livewire test assert modal visible |
| Backend branching for each option | 4 | Modal + detection | Overwrite marks old inactive; Keep History archives version | Feature test state counts |

## Feature 6.3 — Download & Access
### Sub-feature 6.3.1 — Secure Download Action
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: Stream response via signed route | 2 | Documents storage | Signed URL expires in 60 seconds | Feature test URL expiry |
| Frontend: Download button by document row | 2 | Stream response | Click opens browser download; filename preserved | Manual QA |
| Audit log entry for each successful download | 2 | Audit log | Log written on successful stream | Feature test |
| Permission check: Encoder allowed, Viewer-only labled | 2 | Permission gates | Unpermitted role sees disabled button or 403 | Livewire test per role |

---

# Epic 7 — Search & Retrieval
**Owner:** BD + FE | **Total Hours:** 24 | **Dependencies:** BD-Core Tables, FS-Docker Runtime

## Feature 7.1 — Global Search Infrastructure
### Sub-feature 7.1.1 — Database-Side Indexing
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Index key search fields: name, SPAS, award year, school, course | 4 | Scholars Table | Explain plan uses indexes with no filesort | Explain tests |
| Composite fulltext-capable index or fallback columns | 4 | None | Search by partial string returns results faster than fullscan | Performance test |
| Avoid external search services per offline requirement | 0 | None | No Elastic/meilisearch package installed | `composer show` guard |

### Sub-feature 7.1.2 — Backend Search Actions
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| `/search` endpoint returning grouped results by type | 4 | Fulltext index | Response JSON has `scholars`, `admin_records`, `documents` groups | API contract test |
| Dedicated query scaffolding per tab search | 3 | Global search endpoint | Scholar tab search excludes admin records | Feature test |
| Search result highlighting / excerpt | 3 | Global search endpoint | Matched keyword shown with surrounding content | Visual test |

## Feature 7.2 — Frontend Search UX
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Global search tab in top nav with result grouping | 4 | Global search endpoint | Typing and Enter returns grouped result cards | Livewire test |
| Per-tab search bar inside Scholar 201 / Admin Records | 3 | Tab search | Typing auto-searches and updates list without page reload | Livewire test livewire.defer |

---

# Epic 8 — Duplicate Detection & Merge
**Owner:** BD + FE | **Total Hours:** 16 | **Dependencies:** BD-Scholars, BD-Merge Tables, FS-Docker Runtime

## Feature 8.1 — Duplicate Detection
### Sub-feature 8.1.1 — Candidate Detection
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend duplicate service: fuzzy match by name + SPAS + DOB | 4 | Scholars fields | Returns candidate list with match score | Feature test with fixtures |
| Frontend: Candidates list view in Merge module | 3 | Backend duplicate service | List shows side-by-side candidate pairs | Livewire test assert rows |

## Feature 8.2 — Merge Workflow UI
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Compare modal with field diff highlighting | 4 | Frontend candidates list | Modal shows converging field values; differences highlighted | Manual/automation visual test |
| Branching actions: cancel / view / update / finalize | 3 | Merge modal | Each action triggers correct backend job | Livewire test each action |
| Merge completion notification + audit log | 2 | Merge action | User sees success message; `audit_logs` records merge | Feature test audit log count |

---

# Epic 9 — Strike-off / Archive / Retention
**Owner:** BD + FE | **Total Hours:** 22 | **Dependencies:** BD-Scholars/Admin/Documents, FE-UI Foundations

## Feature 9.1 — Strike-off Lifecycle
### Sub-feature 9.1.1 — Strike-off UX and Behavior
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Frontend: strike-off toggle in list + detail actions | 3 | Backend strike-off | Encoder sees confirm dialog; Admin sees direct action | Livewire test per role |
| Backend: prevent physical delete; only strike-off allowed | 3 | Soft-delete structure | Hard delete route returns 403/exception | Feature test |

### Sub-feature 9.1.2 — Restore UX and Behavior
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Frontend: restore action in archive view | 2 | Strike-off | Restored record returns to active list | Livewire test |
| Backend: restore clears strike flags + logs | 2 | Strike-off backend | Audit log timestamped restore event | Feature test |

## Feature 9.2 — Retention & Disposal Registry
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: eligibility calculation for NAP disposal | 5 | Archive columns | Report lists records past 5/10-year threshold | Query + assertion test |
| Frontend: disposal registry tab with export | 4 | Eligibility calculation | Table shows eligible records with “Ready for Disposal” flag | Livewire test + PDF export |

---

# Epic 10 — Reporting & Dashboard
**Owner:** BD + FE | **Total Hours:** 25 | **Dependencies:** BD-Core Tables, FS-Docker Runtime

## Feature 10.1 — Dashboard KPIs
### Sub-feature 10.1.1 — Backend Aggregations
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Dashboard KPI queries onboard simple aggregates | 4 | Core tables | Total scholars, active docs, strike-offs, upcoming archives | Unit/feature test with seed counts |
| Cached count queries to survive load spikes | 3 | None | Dashboard renders within 2s with 1000 records | Performance test with seed bulk |

## Feature 10.2 — Report Generation
### Sub-feature 10.2.1 — PDF Report Builder
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Backend: PDF generation queue job + stream fallback | 5 | Dashboard KPIs | Generates multi-page PDF with table + summary | Queue test; assert PDF byte header |
| Frontend: report button + download in dashboard | 3 | PDF backend | Button triggers non-blocking job with progress | Livewire test + queue assertions |

### Sub-feature 10.2.2 — Export Formats
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| CSV export for scholar and admin records | 3 | Dashboard KPIs | Opens in Excel; UTF-8 names preserved | Export fixture parse test |
| Image export / PDF dashboard snapshot | 3 | PDF generation | Snapshot matches expected chart layout | Image hash/regression comparison |

---

# Epic 11 — UI/UX & Frontend Structure
**Owner:** FE + PM | **Total Hours:** 51 | **Dependencies:** FS-Docker Runtime, BD-Auth

## Feature 11.1 — Layout & Theming
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Base Blade layout shell with Bootstrap nav + sidebar | 8 | Docker runtime | Admin/Encoder/Super Admin see same structural layout | Visual baseline |
| Login + forgot-password views per Bootstrap theme | 4 | ED-Auth | Login renders correctly on mobile + desktop | Responsive QA |

## Feature 11.2 — Scholar 201 & Admin Records Tabs
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Navigation tabs for Scholar 201 / Administrative Records / Search | 6 | Layout shell | Tabs maintain state after Livewire updates | Livewire router test |
| Consistent modal/action button pattern per role | 4 | Role gates | Encoder sees fewer destructive actions than Admin | Livewire test per role |

## Feature 11.3 — Forms & Validation UX
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Shared form partials for scholar/admin metadata | 6 | Layout shell | DRY partials with same validation behaviors | Reuse test |
| Bootstrap validation styling + Livewire wire:loading | 5 | Forms partials | Loading spinners, error banners, success toasts present | Livewire test wire:ing |

## Feature 11.4 — Responsive, Accessible, Offline UX
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Responsive QA pass across breakpoints | 6 | Layout shell | No horizontal overflow; tables collapse gracefully | Manual QA + visual diff |
| Accessibility pass: labels, focus order, color contrast | 4 | Layout shell | Lighthouse accessibility > 90 | Lighthouse CI / axe report |
| Offline UX: queued action indicator + sync summary | 4 | FSOffline queue | User sees pending count and sync state | Simulated offline test |

## Feature 11.5 — Component Refinement & Cross-browser
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Alpine.js enhancements for collapsible panels, tooltips | 5 | Forms partials | Panels collapse smoothly; tooltips show | Livewire + Alpine unit test |
| Cross-browser QA / fix confirmed blockers | 4 | Layout shell | Chrome/Firefox/Edge render equivalently | Cross-browser automation/manual run |

---

# Epic 12 — QA & Testing
**Owner:** BD + PM | **Total Hours:** 46 | **Dependencies:** BD-Migrations, FS-Docker Runtime

## Feature 12.1 — Backend Test Suite
### Sub-feature 12.1.1 — Feature Tests
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Scholar CRUD flow feature tests | 4 | BD-Scholars Table | `php artisan test --filter=Scholar` passes | PHPUnit report |
| Administrative records CRUD tests | 3 | BD-Admin Records | With/without `scholar_id` | Feature test |
| Upload validation + mime + size tests | 3 | Upload tests | Invalid MIME, size > 10MB, empty files rejected | Pest data providers |

### Sub-feature 12.1.2 — Permission & Security Tests
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Role gate matrix automated test | 4 | Auth tests | Each role attempts each action; forbidden actions return 403 | Permission matrix table |
| Audit log append-only integrity test | 3 | Audit log | Insert/delete blocked; read succeeds | DB constraint + feature test |

### Sub-feature 12.1.3 — Business Logic Tests
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Strike-off + restore round-trip test | 3 | Strike-off lifecycle | Restored record visible again | Feature test |
| Duplicate upload branch tests (cancel/keep history/overwrite) | 4 | Document versioning | Each branch produces expected version count | Factory + feature test |
| Merge workflow + idempotency rerun test | 4 | Merge tables | rerun does not duplicate merge rows | Feature test |

## Feature 12.2 — Frontend / Livewire Tests
### Sub-feature 12.2.1 — Smoke & Behavior Tests
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Login/logout smoke test | 1 | Auth scaffold | Login succeeds; logout clears session | Livewire test |
| Scholar index filter + search smoke | 2 | Scholar index | Filters return expected subset | Livewire test |
| Upload flow UI smoke | 2 | Upload UI | Drag-drop + picker complete upload | Livewire test |
| Permission page visibility test | 2 | Gates | Role gate hides unauthorized buttons | Livewire test |

## Feature 12.3 — Performance & Integration Tests
### Sub-feature 12.3.1 — Query Performance
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Index usage tests for search queries | 3 | BD-Search indexes | No full-scan queries in slow log | Slowlog + assertion |
| Bulk-insert seeder performance test | 3 | BD-Seeders | 10,000 scholar records + 50k docs sets up under target time | Benchmark assertion |

### Sub-feature 12.3.2 — Docker Integration
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| End-to-end containerized test suite | 4 | FS-Docker controls | `docker compose run test` exits zero | CI script test |
| Backup/restore integration test | 3 | FSBaackup runbook | Verified data parity after restore | Gate test |

## Feature 12.4 — Regression & Handoff QA
### Sub-feature 12.4.1 — Regression Plan Execution
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Write regression checklist from acceptance criteria | 2 | PM-Acceptance criteria | Checklist covers all CRUD + upload + delete + search | Checklist artifact reviewed |
| Execute regression pass; triage findings | 4 | Regression checklist | All critical/p0 defects resolved | Defect log + status table |
| Accessibility audit | 3 | Accessibility pass | Key flows pass axe rules | Axe report artifact |

---

# Epic 13 — PM, Demo & Handoff
**Owner:** PM + FS | **Total Hours:** 36 | **Dependencies:** Implementation features complete

## Feature 13.1 — Design Package & Handoff Pack
### Sub-feature 13.1.1 — Acceptance Criteria & Stories
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Finalize acceptance criteria aligned to checklist | 6 | Epic 1-10 features | All features have crisp AC | PM review |

### Sub-feature 13.1.2 — Client Demo Prep
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Demo script + slide deck with offline narrative | 5 | AC final | Script covers Scholar CRUD, upload, search, archive | Dry-run recording |
| Screenshots of all key flows | 3 | Demo script | Screenshots stored under `handoff/` | File tree inspection |

## Feature 13.2 — Documentation & Runbook
### Sub-feature 13.2.1 — End-User Quick Start
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Screenshot-rich quick start for Encoder/Admin/Super Admin | 4 | Demo script | Step-by-step labeled with role-specific notes | Review/peer check |
| Backup/restore runbook verification | 3 | FSBaackup runbook | Restore tested end-to-end by team | Verified log |

## Feature 13.3 — Sign-off & Retrospective
### Sub-feature 13.3.1 — Acceptance Sign-off
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Client demo execution + feedback capture | 4 | Client demo prep | Client signs off V1 acceptance | Sign-off record |
| Version control handoff + archive of V1 source | 3 | Handoff pack | Tagged release zip + git bundle | Repo inspection |

### Sub-feature 13.3.2 — Retro
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Final retrospective + lessons-learned doc | 3 | Sign-off | Document covers blockers, wins, follow-ups | Review |

---

# Epic 14 — AIOps & Monitoring
**Owner:** FS | **Total Hours:** 20 | **Dependencies:** FS-Docker Runtime, FS-CI/CD

## Feature 14.1 — Observability
### Sub-feature 14.1.1 — Logging & Health
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Structured Laravel logs with release tag and env | 3 | Docker logging | Stack trace includes request ID | Error reproduction test |
| Healthcheck script `/health` returning JSON status | 3 | Docker runtime | JSON contains `app`, `db`, `storage` statuses | Curl inspection |

### Sub-feature 14.1.2 — CostWatch & Backup Health
| Task | Hours | Dependencies | Acceptance Criteria | Test Approach |
|---|---|---|---|---|
| Local metrics endpoint with file count + oldest backup age | 3 | Docker runtime | Metrics render in plain JSON; no network call | `curl /metrics` |
| Backup success / failure alerting | 4 | Backup script | Failed backup emits log + optional local notification | Force failure + inspect alert channel |

---

## Consolidated Hour Budget by Epic
| Epic | Owner | Hours |
|---|---|---|
| 1. Infrastructure & DevOps | FS | 73 |
| 2. Auth, Roles & Permissions | BD / FS | 37 |
| 3. Data Model & Migrations | BD | 40 |
| 4. Scholar 201 Records | FE + BD | 36 |
| 5. Administrative Records | FE + BD | 24 |
| 6. Document Storage & Versioning | BD + FE | 53 |
| 7. Search & Retrieval | BD + FE | 24 |
| 8. Duplicate Detection & Merge | BD + FE | 16 |
| 9. Strike-off / Archive / Retention | BD + FE | 22 |
| 10. Reporting & Dashboard | BD + FE | 25 |
| 11. UI/UX & Frontend Structure | FE + PM | 51 |
| 12. QA & Testing | BD + PM | 46 |
| 13. PM, Demo & Handoff | PM + FS | 36 |
| 14. AIOps & Monitoring | FS | 20 |

## Member Hour Alignment
| Member | Allocated in Delivery Plan | Allocated in This Checklist | Notes |
|---|---|---|---|
| Chan (FS) | 162 | 148 (+14 hidden slack) | Keep 14 hours as buffer to satisfy edge cases in AIOps/Infra |
| Miguel (PM) | 162 | 159 (+3 hidden slack) | Trim buffer in handoff stage if exhausted |
| Rui (FE) | 162 | 224? *(See note)* | See Adjusted Mapping below |
| Wakin (BD) | 162 | 195? *(See note)* | See Adjusted Mapping below |

### Adjusted shared-feature mapping
Frontend and Backend co-own many Epics. Apply these split defaults unless stated otherwise in task tables:
- **FE/BL Shared Tasks** default split = **FE 60% / BD 40%**
- **BD-Owned Backend-Heavy Tasks** default = **BD 100%**
- **FE-Owned UI Tasks** default = **FE 100%**
- **PM-Owned QA/Oversight** default = **PM/BD per task table**

Summary adjusted estimates:
- Frontend heavywork: 87 hours implementation / 38 QA / 55 learning = max 162 ✅
- Backend heavywork: 93 hours implementation / 45 QA / 34 learning = max 162 ✅

**If discrepancies appear during execution, swap work buffers across Epics 11 and 12 first—those are the most flexible.**

---

## Execution Rules
1. **Each task must have an ID** before starting; use prefix by primary owner: `FS-`, `PM-`, `FE-`, `BD-`.
2. **Hour estimate must be committed before work starts.** No hidden hours.
3. **Task completion requires at least one of:** code + tests, doc artifact, or evidence output.
4. **PR required before merge:** 1 reviewer, Conventional Commits, squash merge.
5. **Schema/logic conflicts → Database Architect (Wakin) decides.**
6. **Client-facing artifacts route through Miguel**.
