# DOSTorage V1 — Weekly Accomplishment Report (Updated)

**Week #:** 1–2 cumulative  
**Date Range:** July 13 – July 21, 2026  
**Programmer:** Chan  
**Assigned Role:** Fullstack / AIOps  
**Supervisor:** Ms. Hazel D. Prevendido  
**Agency:** Department of Science and Technology – Region XI (DOST RXI)  
**Intern Batch:** USeP BSCS OJT Team  
**Hours consumed:** 60 total per member / 240 team shared hours

---

## Cohort Note
All team members are now treated as having consumed **60 hours each**, so this report reflects **equal progress** unless a teammate-specific divergence is explicitly noted.

---

## What I Set Up

The early phase was less about feature-coding and more about giving the team a stable, shared operating system. I concentrated on repo baseline, automation, and the rules that keep us from tripping over each other once implementation starts in earnest.

### Repository and Environment
- Scoped the application skeleton and installed Laravel at the project root so everyone was on the same baseline.
- Set up Docker Compose with MySQL 8.4 as the reproducible local stack.
- Established Git workflow hygiene: `.gitignore`, commit discipline, PR checklist, and branch conventions.
- Studied the Laravel/Bootstrap/Livewire stack boundaries so I could route work to the right owner.

### CI/CD and Automation
- Created a GitHub Actions pipeline for linting, tests, and Bible Keeper audits.
- Built helper scripts for Gantt export and schedule generation so we do not lose a day to manual updates.
- Designed the Bible Keeper automation concept: standalone `bible_keeper` script plus watcher daemon intent.

### Project Bible and Documentation Hygiene
- Helped design the Project Bible structure that teammates filled in: modules, flowcharts, schema drafts, and the MLS for administrative files.
- Drafted Tech Documentation and incorporated low-fidelity prototype notes after client/IT meetings.
- Redesigned hour tracking so learning hours and implementation hours are distinguishable.
- Diagnosed the Bible Keeper tab-scoping issue on Windows/Hermes and implemented safer export-only behavior until a tab-safe write path is finalized.

---

## Learning Phase Deliverables
- Laravel/Livewire/Spatie/Docker mirror project
- Docker Compose with MySQL
- Bible Keeper v1
- Deployment runbook draft
- CI healthcheck script
- Test plan + smoke tests

---

## Implementation-Phase Work Started at 60 Hours

These implementation tasks were seeded from:
- `planning/team_fullstack.csv`
- `C:\Users\Asus\Downloads\DTR [USeP] (1).txt`
- `planning/team_pm.csv`
- `planning/team_frontend.csv`
- `planning/team_backend.csv`

### Chan / Fullstack
- **FS-06:** Repo + Docker + CI started
  - Evidence: seeders/migrations practice, CRUD exploration, Bible Keeper patch
  - Status: `In Progress`

### Miguel / PM + merged QA
- **PM-06:** Design package approved started
  - Evidence: KPI review, dashboard data types, feature checklist, style guide draft
  - Status: `In Progress`
- **MQ-01:** Test plan + cases doc started
  - Evidence: merged QA ownership from `team_qa.csv`
  - Status: `In Progress`

### Rui / Frontend
- **FE-06:** Layout + theme + login started
  - Evidence: AI coding assistant tool setup, project workflow clarification
  - Status: `In Progress`

### Wakin / Backend
- **BD-06:** All V1 migrations + rollback started
  - Evidence: database schema refinement, schema change exploration, 12-model relationship definition, migration drafting
  - Status: `In Progress`

---

## Learning Cutoff
- Fast-tracked learning phase ended at **40 hours per member**.
- Everyone is now in the **implementation phase**, with 60 total hours consumed per person.

---

## What Went Wrong and How I Fixed It
- **Bible-write drift:** Live writes from the Windows/Hermes client landed in `Bible Center` instead of `TASKS DETECTED`. I stopped relying on unscoped tab writes and added safer export-first behavior.
- **Planning drift:** Core docs still referenced 81-hour learning phases. I corrected `TEAM_WORKFLOW.md`, `long_range_planning.md`, and all `team_*.csv` files to the 40-hour fast-track cutoff.
- **QA ownership ambiguity:** `team_qa.csv` had QA tasks unowned. I aligned it with Miguel’s merged QA/PM role from the DTR record.

---

## Looking Ahead

| Focus Area | Next Action |
|---|---|
| Repo + Docker init | Complete FS-06 with volume-mounted migrations |
| Bible Keeper cleanup | Restore `Bible Center`; choose tab-safe write path |
| Backend scaffold | Wakin completes BD-06 migrations and rolls back |
| Layout shell | Rui completes FE-06 once stable API exists |
| QA baseline | Miguel begins MQ-01 documentation immediately |
| Client demo prep | Miguel begins PM-06 design package sign-off |

---

*Report prepared by the Fullstack / AIOps intern based on planning artifacts, teammate DTR record `DTR [USeP] (1).txt`, and the live Project Bible.*
