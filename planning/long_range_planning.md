# DOSTorage V1 — Long Range Planning (162 Hours per Person)
> Total team capacity: 4 members × 162 hours each = 648 total hours  
> Hard deadline: V1 delivery when shared hours hit 648

## Golden Rules
- **Each member must burn exactly 162 hours** — no exceptions
- **Half internship = learning** — 40 hours per person dedicated to tech stack study
- **Performance override** — if a member completes their learning goals early, they may start implementation tasks
- **Minimum 1 deliverable per day per member** — no empty days
- **Deliverable = testable, reviewable artifact** — not just “worked on X”
- **Daily standup input = 3 bullets per member**: done / blocked / next
- **Scope freeze** — no new features after learning phase ends, unless team votes to extend
- **Shared-hours rule** — every hour deducted from the same 648-hour pool

## Team Structure
- **Fullstack / AIOps (you)**: repo, Docker, CI, deployment runbook, bible keeper system, automation
- **Project Manager / UI-UX (Miguel)**: mockups, design, client alignment, acceptance criteria
- **Frontend (Rui)**: Blade layouts, Bootstrap theme, Livewire views, responsive QA
- **Backend & DB (Wakin)**: models, seeders, storage logic, strike-off, audit log, QA test writing

## Current Team Grounding
- **Stack decision:** Laravel 13 / Livewire 4 / Spatie / Bootstrap 5 (no stack downgrades)
- **Fullstack / AIOps (you):** Docker runtime complete; migrations/seeds green; test suite green; backup/restore scripts in progress
- **Project Manager / UI-UX (Miguel):** drawing physical low-fi prototype
- **Frontend (Rui):** studying front-end (Blade, Bootstrap, Livewire)
- **Backend & DB (Wakin):** studying back end (Laravel, Eloquent, migrations)

## Phase status
- **Phase 1: Learning — COMPLETE**
- Remaining implementation budget: **488 shared hours / 122 per member**

## 648-Hour Burn Plan  
| Phase | Hours Burned | Remaining | Milestone |  
|-------|-------------|-----------|-----------|  
| 1: Learning | 160 | 488 | Fast-tracked learning complete; wireframes approved; schema drafted |  
| 2: Implementation | 488 | 0 | V1 complete; handoff pack ready |  

## Phase 1 — Learning & Preparation (up to 160 shared hours)  
> Fast-tracked learning is capped at **40 hours per member**, not 81 hours.  
> Pure learning, design, and planning.  
> If a member finishes their 40h learning early, they may begin implementation tasks early.

### Fullstack / AIOps (you) — target 40 learning hours
| Hours | Hourly Goal | Deliverable | Learning Goal |
|-------|-------------|-------------|---------------|
| 1–12 | Study Laravel/Livewire/Spatie/Docker fundamentals. Build mirror project (login + scholar). Document findings in Engineering Wiki | Mirror project + wiki entries | Laravel ecosystem confidence |
| 13–20 | Study Docker networking, CI scripting, bible keeper system | Bible Keeper v1 working | Docker compose for Laravel |
| 21–27 | Study deployment hardening, LAN access, backup/restore | Deployment runbook draft | Nginx + PHP-FPM tuning |
| 28–33 | Study CI/CD pipelines, healthcheck scripts, automation | CI healthcheck script | Bash scripting for ops |
| 34–40 | Study Laravel testing (Pest/PHPUnit), Livewire test utilities | Test plan + smoke tests | Testing best practices |

**Early implementation trigger:** Mirror project complete + wiki entries submitted

### Project Manager / UI-UX (Miguel) — target 40 learning hours
| Hours | Hourly Goal | Deliverable | Learning Goal |
|-------|-------------|-------------|---------------|
| 1–12 | Study Bootstrap 5, user flows, accessibility. Finalize physical low-fi prototype. Document acceptance criteria draft | Low-fi prototype + acceptance criteria | Bootstrap + user flow mapping |
| 13–20 | Study Figma (if needed), DOST brand guidelines. Draft design system tokens | Design tokens doc + finalized wireframes | Design system basics |
| 21–27 | Study responsive design, breakpoints, accessibility testing | Responsive QA checklist | Bootstrap breakpoints |
| 28–33 | Study client demo preparation, handoff documentation | Demo script draft | Client communication |
| 34–40 | Study test plan creation, bug triage, regression planning | Test plan + regression checklist | QA process |

**Early implementation trigger:** Design package approved + acceptance criteria signed off

### Frontend (Rui) — target 40 learning hours
| Hours | Hourly Goal | Deliverable | Learning Goal |
|-------|-------------|-------------|---------------|
| 1–12 | Study Blade templating, Bootstrap 5, Livewire basics. Build sample pages from wireframes | Sample pages + Blade practice | Bootstrap + Livewire integration |
| 13–20 | Study file API, drag-drop JS, Alpine.js. Draft upload component plan | Component map + Alpine notes | Livewire upload patterns |
| 21–27 | Study Livewire pagination, filters, form validation | Pagination + filter examples | Livewire advanced features |
| 28–33 | Study responsive QA, cross-browser testing | Responsive QA report | Browser compatibility |
| 34–40 | Study testing frontend (Pest + Livewire test utilities) | Frontend smoke tests | Frontend testing |

**Early implementation trigger:** Component map complete + Alpine notes submitted

### Backend & DB (Wakin) — target 40 learning hours
| Hours | Hourly Goal | Deliverable | Learning Goal |
|-------|-------------|-------------|---------------|
| 1–12 | Study PHP/Laravel fundamentals. Learn Eloquent, migrations, validation, file storage. Build mirror project backend | Mirror project backend + seeders | Laravel backend confidence |
| 13–20 | Study Spatie permissions, testing (Pest/PHPUnit), Livewire backend. Draft migrations for V1 | V1 migration drafts | Spatie + testing best practices |
| 21–27 | Study Laravel filesystem, UUID strategies, soft-delete patterns | Storage config doc | Laravel filesystem disks |
| 28–33 | Study polymorphic relationships, version metadata, audit logs | Schema extension proposal | Database versioning |
| 34–40 | Study search indexing, fulltext vs column index, query optimization | Search strategy doc | MySQL performance |

**Early implementation trigger:** Migration drafts complete + schema extension proposal approved

## Phase 2 — Implementation (remaining shared hours)  
> Begins at **hour 41** for all members.  
> Total implementation budget: **488 shared hours** (**122 per person**).

### Fullstack / AIOps
| Hours | Hourly Goal | Deliverable |
|-------|-------------|-------------|
| 82–89 | Repo init, git workflow, Dockerfile + compose, CI healthcheck | Repo + Docker + CI |
| 90–97 | Laravel Breeze install, Spatie setup, auth test | Auth working in Docker |
| 98–105 | Backend migrations volume-mounted, MySQL init, run testing | Migrations run cleanly |
| 106–111 | CI script testing, backup script, restore test | CI + backup + restore tested |
| 112–117 | Docker hardening, LAN access test | Docker hardened; accessible on LAN |

### Project Manager / UI-UX
| Hours | Hourly Goal | Deliverable |
|-------|-------------|-------------|
| 82–89 | Finalize wireframes, Bootstrap theme, component list | Design package approved |
| 90–97 | Review frontend layout shell, nav, login view | Layout approved |
| 98–105 | Review upload UI, strike-off UI, responsive QA | Upload UI approved |
| 106–111 | Review permission matrix, regression prep | Regression plan |
| 112–117 | Handoff pack: screenshots, acceptance criteria, demo prep | Handoff pack complete |

### Frontend
| Hours | Hourly Goal | Deliverable |
|-------|-------------|-------------|
| 82–89 | Blade layout shell, Bootstrap theme, nav + login view | Layout + theme + login |
| 90–95 | Scholar index Livewire, filters, show page scaffold | Index + show views working |
| 96–103 | Upload form, drag-drop JS, 10MB validation UI | Upload form complete |
| 104–108 | Strike-off button UI, restore modal, admin-only perm check | Strike-off + restore UI |
| 109–117 | Responsive QA, cleanup | Responsive QA pass |

### Backend & DB
| Hours | Hourly Goal | Deliverable |
|-------|-------------|-------------|
| 82–89 | Scholars migration, schools/courses/regions, statuses/file_types, rollback test | All V1 migrations + rollback |
| 90–95 | Scholar model + relationships, Document model, seeders | Models + seeders |
| 96–103 | UUID storage config, soft-delete migration, strike-off logic | Storage + strike-off done |
| 104–109 | Duplicate detection, cancel/history/overwrite, version metadata | Duplicate workflow backend |
| 110–117 | Audit_log migration, login + file action logging | Audit log scaffold |

## Shared Pool Summary
| Phase | Total Shared Hours | Per Member |
|-------|-------------------|------------|
| Phase 1: Learning | 160 | 40 each |
| Phase 2: Implementation | 488 | 122 each |
| **Total** | **648** | **162 each** |

## Decision Gates
| Gate | Criteria |
|------|----------|
| Hour 40 Gate | Each member has completed 40 learning hours with deliverables. If not, learning extends. |
| Hour 162 Gate | Learning phase ends; implementation begins for ready members |
| Hour 324 Gate | Half of implementation complete |
| Hour 486 Gate | Regression + Docker hardening complete |
| Hour 648 Gate | V1 handoff pack complete |

## Bible Keeper Schedule
- **12:00 PM** — run against Meetings tab
- **7:00 PM** — run against Meetings tab + audit daily commits
- On-demand — run when new decisions appear in meetings

## Bible Thickness Control — Active Task
**Problem:** Bible grows each meeting; more pages = more AI context = higher hallucination risk.

**Methodology:** Living Index + Archive
- Bible Center keeps only **active, unresolved, or recently validated** items
- Resolved/old items move to **Project Bible Archive** with a one-line pointer
- Weekly Bible Keeper run also trims: old meeting summaries → 3–5 line summaries in Archive
- Anything older than **30 days** and marked `[CONFIRMED]` or `[DECISION]` is a trim candidate

**Assigned task:**
- **Fullstack / AIOps (you)** owns the Bible Keeper cron + trim workflow
- **Wakin** reviews any schema/ERD changes before they stay in Bible
- **Miguel** verifies trim does not delete needed context by spot-checking Archive weekly

**Trim schedule:**
- **Daily 7:00 PM Bible Keeper run** — flag candidates
- **Friday standup** — approve/archive list
- **Archive doc:** `Project Bible Archive V0.x` in same Google Drive folder

**Rules:**
- Never delete raw meeting notes; compress them
- Never remove `[OPEN]` / `[DECISION]` items; only archive resolved ones
- If a decision is reopened, pull it back from Archive with `[REOPENED]` tag

## Bible Keeper Conflict Resolution
When the Keeper flags a conflict mid-implementation:
1. **Review path:** Team reviews the conflict in next standup. If Keeper was right, programmer adjusts. If Keeper was wrong, team votes to override.
2. **Abide path:** If the programmer has already implemented something that contradicts the Bible, the team can vote to keep the implementation and update the Bible. Never force a rework without team vote.

**Tag approval:** Whole team approves tags. Keeper proposes; team accepts/rejects/modifies.

## Blocker Handling
- **Definition:** A deliverable missed by any member is a **team blocker**.
- **Priority:** All other work stops until blocker is resolved.
- **Protocol:**
  1. Blocker declared in standup
  2. All available members assist the blocked member
  3. If blocker cannot be resolved within 4 hours, blocked member takes a break while others unblock
  4. Blocker resolution is logged in burndown sheet

## Reallocation Protocol
- **Ahead of schedule:** Member continues their current task until they hit a blocker
- **At blocker:** Team votes on reallocation
- **Options:** Pair with blocked member, take over blocked task, or continue own work if blocker is minor

## Client-Confirmation Questions (hard stop — do not proceed without answers)
These items cannot be decided by the team alone. Client must confirm each one.

| # | Question | Why it needs client confirmation |
|---|----------|--------------------------------|
| 1 | Is V1 = only Scholar 201 + Administrative Records, or is there a minimum demo subset? | Scope boundary affects every task estimate |
| 2 | Search in V1 or future? | Affects whether indexing work happens now or later |
| 3 | Any mandatory report must ship in V1? | Report generation can consume 15–20 hours |
| 4 | Client demo date | Entire team schedules around this |
| 5 | Acceptance criteria format — checklist or demo? | Determines how QA prepares handoff |
| 6 | Change control protocol — swap scope or burn extra hours? | Protects the 648-hour pool from hidden work |
| 7 | Sign-off definition — what does “done” look like? | Prevents scope creep at handoff |

**Protocol:** No team member starts work on a client-dependent item until the answer is logged in Bible Center with `[CONFIRMED]` and a date.

## Client Communication
- Cadence: online office check-ins as needed
- All client contact goes through Miguel (PM/UI-UX) unless otherwise assigned
- Client confirmations are logged in Bible Center with timestamp

## Deferred Items
- 14-cabinet physical document scanning/indexing: deferred to client

## Open Floor / Decision Tags
Use only: `[OPEN]`, `[DECISION]`, `[CONFIRMED]`, `[FUTURE]`, `[ARCHIVED]`, `[REOPENED]`
Never edit meeting-truth without open-floor process.

## Appendix: Task ID Convention
- `FS-` = Fullstack / AIOps
- `PM-` = Project Manager / UI-UX
- `FE-` = Frontend
- `BD-` = Backend & DB
- `DAY-` = Shared / burndown
- `ADR-` = Architecture Decision Record
- `OPEN-` = Open-floor item
