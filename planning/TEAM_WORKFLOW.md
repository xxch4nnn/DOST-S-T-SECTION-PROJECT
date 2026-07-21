# DOSTorage V1 — Team Workflow
> **Hours per person:** 122 implementation hours each after fast-tracked learning. **Total shared pool:** 648 hours  
> **Learning cutoff:** 40 hours per person  
> **Implementation budget:** 648 − 40×4 = 488 shared hours  
> **Location:** Same physical place. Daily standups.

## Current Team Grounding (as of planning meeting)

| Member | Current Study Task | Assigned Role |
|--------|-------------------|---------------|
| Member 1 (You) | Studying project bible keeper + tech stack | **Fullstack / AIOps** |
| Member 2 (Miguel) | Drawing physical low-fi prototype | **Project Manager / UI-UX** |
| Member 3 (Rui) | Studying front-end | **Frontend** |
| Member 4 (Wakin) | Studying back end | **Backend & DB + QA tests** |

---

## Daily Rhythm

### 1. Standup (15 min, fixed time)
- Each member posts 3 bullets in the shared burndown sheet Notes column:
  - **Done yesterday**
  - **Blocked today**
  - **Next deliverable today**
- Bible Keeper runs at **12:00 PM** and **7:00 PM**. Flags raised at standup.

### 2. Work Blocks (6–8 hours)
- Pair-programming encouraged when roles overlap
- Stop at the first blocker; do not silently switch tasks

### 3. Deliverable Check (end of day)
Before leaving, each member must have:
- **Code** with tests, or
- **Doc** in the team folder, or
- **Evidence** (screenshots, passing test output, smoke report)
Nothing else counts.

### 4. Blocker Protocol
- A missed deliverable = **team blocker**
- All available members assist the blocked member
- If unresolved within 4 hours, blocked member takes a break while others unblock
- Blocker and resolution are logged in burndown sheet

### 5. Bible Keeper Audit (7:00 PM cron)
- Runs automatically
- If conflicts found, team reviews in next standup
- Whole team approves/disapproves/modifies proposed tags

---

## Role Definitions

### Fullstack / AIOps (you)
- Repo scaffolding, git setup, Docker, CI, deployment runbook, bible keeper system, automation
- Owns: `Dockerfile`, `docker-compose.yml`, `.gitignore`, healthcheck script, Bible Keeper cron
- Does NOT own: Blade templates, migrations, QA test writing

### Project Manager / UI-UX (Miguel)
- Mockups, design system, client alignment, acceptance criteria, QA acceptance
- Owns: Figma files, design tokens, acceptance criteria doc, handoff pack
- Does NOT own feature code
- All client communication routes through Miguel unless reassigned

### Frontend (Rui)
- Blade layouts, Bootstrap theme, Livewire views, responsive QA
- Owns: `resources/views/`, `resources/css/`, `resources/js/`
- Boundary: no PHP logic beyond Livewire component hooks; backend owns validation

### Backend & DB (Wakin)
- Migrations support, models, seeders, storage logic, strike-off, audit log, QA test writing
- Owns: `database/migrations/`, `app/Models/`, `app/Livewire/**/*.php` backend, `tests/`
- Does NOT own: Blade templates, Dockerfile

---

## Shared Pool Rules

### Hour deduction
- Actual hours are entered in the burndown sheet by Fullstack at end of day
- Formula: `Remaining = 648 - SUM(Actual Hours all members)`
- No task starts without an hour estimate in the sheet

### Learning phase
|- Each member gets **40 learning hours** in the fast-tracked learning phase  
|- Learning phase ends at **40 hours** per person or when all learning deliverables are complete, whichever comes first  
|- Member may begin implementation tasks early if learning deliverables are complete  
|
|### Implementation phase  
|- Begins at **hour 41** for every member  
|- Remaining hours go to implementation  
|- Total implementation budget: **488 shared hours** (**122 per person**)

### Scope freeze
- No new features after learning phase ends unless team votes to extend

### Change protocol
|- Mid-sprint new feature request = swap for existing scope **or** burn extra shared hours
|- No hidden hours. If it is not in the sheet, it did not happen.

### Archive rule
|- Completed learning-phase tasks are archived in the shared burndown/history sheet
|- Bible Center `[ARCHIVED]` items stay in place for audit; do not delete them manually
|- If a project artifact is superseded, move it to `planning/archive/` and leave a one-line pointer in the original location

---
## Git Workflow

### Branch naming
- `feat/<task-id>-<short-desc>` — example: `feat/FS-01-repo-scaffold`
- `fix/<task-id>-<short-desc>`

### Commit discipline
- Format: `<type>(<scope>): <message>`
- Types: `feat`, `fix`, `docs`, `refactor`, `test`, `chore`
- Example: `feat(auth): add Laravel Breeze login scaffolding`

### Pull requests
- Mandatory 1 reviewer before merge
- PR title must include task ID
- Squash merge only

---

## Code Review

### Checklist before requesting review
1. Tests pass: `php artisan test`
2. Lint clean: no new warnings
3. No hardcoded secrets / `.env` leaks
4. Migration rollback-safe: `php artisan migrate:rollback` works
5. README / docs updated if behavior changed

### Reviewer responsibility
- Review within 2 hours if possible
- If blocked, state exactly what is missing
- Approve only if all checklist items pass

---

## Conflict Resolution

### During implementation
1. Pause the conflicting task
2. Each side states 1-sentence position in standup
3. If no consensus in 5 minutes:
   - **Process/tooling conflicts:** Fullstack decides
   - **Schema/logic conflicts:** Backend & DB (Wakin) decides
   - **Acceptance conflicts:** PM / UI-UX (Miguel) decides
4. Bible Keeper logs the decision as `[DECISION]` with timestamp

### Bible Keeper conflicts mid-implementation
1. **Review path:** Team reviews the conflict in next standup. If Keeper was right, programmer adjusts. If Keeper was wrong, team votes to override.
2. **Abide path:** If programmer has already implemented something that contradicts the Bible, team can vote to keep implementation and update Bible. Never force rework without team vote.

### During long-range planning
- Bible Keeper logs as `[OPEN]`
- Team holds a 15-minute floor debate
- Decision logged before moving to next topic

---

## Reallocation Protocol
- **Ahead of schedule:** Member continues current task until they encounter a blocker
- **At blocker:** Team votes on reallocation
- **Options:** 
  1. Pair with blocked member
  2. Take over blocked task
  3. Continue own work if blocker is minor

---

## Acceptance Criteria

### V1 is “done” when:
1. Scholar 201 full CRUD works offline
2. Administrative Records upload + metadata works
3. Document strike-off and restore works
4. Duplicate upload offers 3 options
5. Docker compose brings up app + database cleanly
6. Spatie roles enforce Super Admin / Admin / Encoder
7. Minimum test set passes
8. Backup + restore runbook written and tested
9. Client demo succeeds
10. Source code + runbook handed over

### Handoff pack
- Source code zip
- `docker-compose.yml` + `Dockerfile`
- Test report with pass/fail counts
- Screenshots of all flows
- Bible Center clean with no unresolved `[OPEN]` items that block V1

---

## Appendix: Task ID Convention
- `FS-` = Fullstack / AIOps
- `PM-` = Project Manager / UI-UX
- `FE-` = Frontend
- `BD-` = Backend & DB
- `DAY-` = Shared / burndown
- `ADR-` = Architecture Decision Record
- `OPEN-` = Open-floor item
