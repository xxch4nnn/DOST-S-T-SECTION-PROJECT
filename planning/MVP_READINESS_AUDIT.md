# DOSTorage V1 — MVP Readiness Audit

**Audited source:** local checked-out repo at `C:\Users\Asus\Documents\Personal\Programs\DOSTorage` on `master`  
**Note:** The requested GitHub URL could not be fetched from this environment, so this audit is based on the local worktree, which contains uncommitted changes and currently has an empty `.github/workflows/test.yml`.

---

## 1. MVP Gap Analysis & Feature Matrix

### By User Role / Access Level
| Role | Current State | Gaps |
|------|---------------|------|
| Super Admin | Has implicit bypass via `Gate::before`; can access audit logs and strike off/restore docs in UI | No user/role management UI; no bulk admin actions; no documented onboarding flow for new Super Admin accounts |
| Admin | Policies allow full scholar/admin-record/doc CRUD; can strike off and restore documents | Missing admin dashboard ownership views; no record-level approval workflows beyond strike-off/restore |
| Encoder | Can create scholars, upload documents, view scholars and admin records index | Cannot create admin records by permission matrix; no offline-queue reconciliation UI; no batch upload validation UX |
| Records Officer / Viewer | Not explicitly implemented as a distinct role with tailored dashboard/search-only workflows | Missing role-specific landing/search experience; current dashboard routing is limited to staff roles |
| Guest / Unauthenticated | Redirected to login; `/health` is public | No public intake/request form; no read-only public lookup if required |

### Backend
| Area | Current State | Gaps |
|------|---------------|------|
| Auth routes | `routes/auth.php` is empty in the checked-out tree | Login/register/password reset routes are missing or not wired; this is an MVP blocker |
| Models & migrations | Core schema exists: users, scholars, admin records, documents, document versions, audit logs, file types/groups, offline queue | No Financial Ledger yet, but V1 scope excludes it; still needs migration safety and seed completeness validation |
| Business logic | Document versioning, UUID thin-document shell, duplicate handling, strike-off/restore, audit logging | No background job processing for `offline_queue_items`; no explicit service layer; some logic is embedded in Livewire components |
| Validation | Livewire validate blocks for scholar/admin/doc uploads | Validation is inconsistent across components; missing centralized form requests; no API validation layer |
| Authorization | Spatie roles + policies + route middleware + `Gate::before` for Super Admin | No documented role/permission matrix enforcement tests beyond a few gates; `viewAuditLogs` permission exists but is not clearly assigned in reviewed code |
| Persistence | MySQL via Docker; local private disk for documents | No automated backup/restore workflow; no DB connection resilience for offline-first queue replay |

### Frontend
| Area | Current State | Gaps |
|------|---------------|------|
| UI views | Blade/Livewire views exist for scholars, admin records, dashboard, auth, profile, notifications | Dashboard shell is minimal; missing dedicated Reports/Admin pages; document file editor is `501 Not Implemented` |
| State management | Livewire component state handles uploads, search, drawers | No global loading/error boundary strategy; retry/offline UX is minimal |
| UX flows | Upload wizard, duplicate modal, scholar drawer, category tabs | No empty-state guidance; no onboarding tour; no confirmation flows for destructive actions beyond basic flash messages |
| Accessibility/UX polish | Bootstrap 5.3 used | Inline hardcoded styles remain in some views; no documented design-token enforcement beyond `docs/DESIGN_TONKS.md` |

### AIOps / MLOps
| Area | Current State | Gaps |
|------|---------------|------|
| Model serving | None | No inference endpoints or model artifacts in repo |
| Prompt orchestration | `ai/PROMPTS/` contains quick references only | No runtime prompt pipeline or evaluation harness |
| Monitoring/logging | Laravel logging + audit logs | No structured JSON logging, metrics, or alerting pipeline |
| Evaluation | None | No automated evaluation for AI-assisted workflows |

---

## 2. Prioritized Implementation Roadmap

### Must-Have for MVP
| # | Item | Technical Task |
|---|------|----------------|
| M1 | Restore auth route wiring | Rebuild `routes/auth.php` with login/register/email verification/password reset routes required by current auth tests and UI |
| M2 | Fix CI pipeline | Replace empty `.github/workflows/test.yml` with lint + static analysis + tests + migration smoke |
| M3 | Role completeness | Ensure Super Admin / Admin / Encoder permissions and default role assignments are seeded and tested end-to-end |
| M4 | Dashboard shell stability | Provide a working dashboard landing for staff roles with KPI cards and navigation fallbacks |
| M5 | Document viewer fallback | If PDF.js viewer is unavailable, ensure `viewFile` inline streaming works with proper headers/CORS-like cache control |
| M6 | Offline queue replay | Implement `ReplayOfflineQueueCommand` behavior and failure handling so offline actions can sync when connectivity returns |
| M7 | Secrets hygiene | Remove or restrict `google_client_secret.json`; enforce `.env`-only secrets and add pre-commit/CI secret scan |

### Should-Have
| # | Item | Technical Task |
|---|------|----------------|
| S1 | User management UI | Super Admin user/role/permission CRUD with invite/activation flow |
| S2 | Reports | Exportable scholar/admin-record reports with filters by year, region, school |
| S3 | Validation refactor | Move repeated Livewire validation into Form Requests or dedicated validator services |
| S4 | Frontend error boundaries | Add Livewire-level error handling and skeleton loaders for async actions |

### Could-Have
| # | Item | Technical Task |
|---|------|----------------|
| C1 | Advanced search | Add full-text search or indexed search across metadata fields |
| C2 | Batch operations | Multi-select strike-off/restore/export for admins |
| C3 | AI-assisted metadata extraction | Prompt-based document classification with human-in-the-loop review |

### Won't-Have Yet
| # | Item | Reason |
|---|------|--------|
| W1 | Financial Ledger | Explicitly out of V1 scope |
| W2 | Public API | V1 is server-rendered web only |
| W3 | Multi-tenant SaaS mode | Single-organization offline/local deployment only |

---

## 3. Revised CI/CD Pipeline

See proposed replacement for the empty test workflow and enhanced deploy workflow:
- `.github/workflows/ci.yml`
- `.github/workflows/deploy.yml`

These should be added/updated in the repo.

---

*Audit generated against local repo state. Re-run after restoring `routes/auth.php` and filling `.github/workflows/test.yml`.*
