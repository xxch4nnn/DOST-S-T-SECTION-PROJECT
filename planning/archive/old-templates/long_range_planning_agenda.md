# DOSTorage V1 — Long-Range Planning Meeting Agenda
> **Total shared hours:** 648 (4 members × 162 each)  
> **Current team:** 4 members, same physical location, daily standups  
> **Current week:** flowchart + MVP achieved; UI/UX working on low-fi prototype  
> **Learning phase:** 81 hours per person (half of internship). Implementation begins when learning deliverables are complete.

---

## Current Team Grounding
- **Fullstack / AIOps (you):** studying project bible keeper system + full stack / tech stack
- **Project Manager / UI-UX (Miguel):** studying mockups, low-fi prototype, design
- **Frontend (Rui):** studying front-end (Blade, Bootstrap, Livewire)
- **Backend & DB (Wakin):** studying back end (Laravel, Eloquent, migrations)

## Meeting rules
- **Timeboxed** — each topic has a fixed window
- **Decisions only** — no discussion without a decision or tagged action
- **Bible Center is sacred** — only add `[OPEN]`, `[DECISION]`, `[CONFIRMED]`, `[FUTURE]`, `[ARCHIVED]`
- **Shared-hours rule** — every hour deducted from the same 648-hour pool
- **Learning phase** — 81 hours per person, but performance allows early jump to implementation

---

## Part 1 — Scope & MVP Boundary (30 min)

| Time | Topic | Question | Decision target |
|------|-------|----------|-----------------|
| 0:00 | V1 feature completeness | Is V1 = only Scholar 201 + Administrative Records, or is there a minimum demo subset for the client? | `[CONFIRMED]` |
| 0:05 | Search | In or out of V1? If out, who lobbies the client and when? | `[DECISION]` |
| 0:10 | Audit trail | Full `audit_logs` table in V1, or basic `updated_at` only? | `[DECISION]` |
| 0:15 | Duplicate workflow | All 3 options (Cancel / Keep History / Overwrite) in V1, or MVP only Cancel + Keep History? | `[DECISION]` |
| 0:20 | Reports | Any mandatory report must ship in V1, or all deferred? | `[DECISION]` |
| 0:25 | Dashboard | Any KPI required for client handoff, or pure future? | `[DECISION]` |

---

## Part 2 — Architecture & Technology (30 min)

| Time | Topic | Question | Decision target |
|------|-------|----------|-----------------|
| 0:30 | CSS framework | Bootstrap vs Tailwind? Frontend cannot start views until this is answered. | `[DECISION]` |
| 0:35 | JS layer | Livewire only, or Alpine.js too? What’s the boundary vs Livewire? | `[DECISION]` |
| 0:40 | Document storage | Flat UUID files confirmed. What directory permissions on DOST server? Will web server user have write access to `storage/app/private`? | `[OPEN]` |
| 0:45 | Admin Records schema | “Document” owned by administrative record instead of scholar. Build polymorphic from Day 1, or scholar-only first and refactor later? | `[DECISION]` |
| 0:50 | Laravel/Livewire/Spatie versions | Exact versions locked for V1? | `[CONFIRMED]` |

---

## Part 3 — Deployment & Infrastructure (20 min)

| Time | Topic | Question | Decision target |
|------|-------|----------|-----------------|
| 1:20 | Docker topology | PHP-FPM + Nginx + MySQL in one compose, or separate containers? | `[DECISION]` |
| 1:25 | MySQL version | What version is on the existing DOST server? Can the app match it, or must it support older? | `[OPEN]` |
| 1:30 | LAN access | Single hostname/port for all 4 devs, or each dev runs local Docker and syncs manually? | `[DECISION]` |
| 1:35 | Backup | Who owns `mysqldump`? Scheduled cron, or manual runbook only? | `[DECISION]` |

---

## Part 4 — Team & Process (25 min)

| Time | Topic | Question | Decision target |
|------|-------|----------|-----------------|
| 1:40 | Named roles | Confirm roles: you (Fullstack/AIOps), Miguel (PM/UI-UX), Rui (Frontend), Wakin (Backend/DB + QA tests). Names against deliverables. | `[CONFIRMED]` |
| 1:45 | Shared-hours tracking | Shared sheet, or independent logs pooled at standup? | `[DECISION]` |
| 1:50 | Daily standup | Fixed time, same place, what timebox? | `[CONFIRMED]` |
| 1:55 | Blocker protocol | If someone misses a deliverable, team stops to fix. Blocked member takes a break. Agreed? | `[CONFIRMED]` |
| 2:00 | Reallocation protocol | Ahead-of-schedule member continues until blocker, then team votes. Agreed? | `[CONFIRMED]` |
| 2:05 | Conflict resolution | If two devs disagree in real-time, who decides? Tech lead vote, or table it? | `[DECISION]` |
| 2:10 | Git branching model | Trunk-based with feature flags, or long-lived branches per role? | `[DECISION]` |
| 2:15 | Code review | Mandatory 1 reviewer before merge, or trust-based? | `[DECISION]` |
| 2:20 | Commit discipline | Conventional commits? Who enforces? | `[DECISION]` |

---

## Part 5 — Client Confirmation Required (15 min)

| Time | Topic | Question | Decision target |
|------|-------|----------|-----------------|
| 2:30 | V1 scope | Is V1 = only Scholar 201 + Administrative Records, or is there a minimum demo subset for the client? | `[CONFIRMED]` — client must confirm scope |
| 2:35 | Search | In or out of V1? If out, who lobbies the client and when? | `[DECISION]` — client must confirm if in V1 |
| 2:40 | Reports | Any mandatory report must ship in V1? | `[DECISION]` — client must confirm |
| 2:45 | Client demo date | Fixed date in the 162 hours, or flexible? | `[DECISION]` — client must confirm date |
| 2:50 | Acceptance criteria | Client written checklist, or demo-based? | `[DECISION]` — client must confirm format |
| 2:55 | Change control | New feature mid-sprint: swap for existing scope, or burn extra shared hours? | `[DECISION]` — client must confirm |
| 3:00 | Sign-off definition | What does “done” look like? | `[CONFIRMED]` — client must confirm |

**Protocol:** Any item in this section is a **hard stop** until client confirms. Team cannot proceed without written/verbal confirmation logged in Bible Center.

---

## Part 6 — Risk Register & Assumptions (15 min)

| Time | Topic | Question | Decision target |
|------|-------|----------|-----------------|
| 3:10 | Learning curve | Half internship = 81h learning per person. If performance allows, early implementation. Agreed? | `[CONFIRMED]` |
| 3:15 | Database architect availability | Wakin owns schema, ERD, migrations. Reachable within 162h, or decide unilaterally? | `[CONFIRMED]` |
| 3:20 | Physical document scanning | 14-cabinet scan/index job deferred to client. Agreed? | `[CONFIRMED]` |
| 3:25 | Program name | What is the official name of the application? | `[DECISION]` |
| 3:30 | Design style | Color palette, typography, logo usage, DOST brand compliance. | `[DECISION]` |
| 3:35 | Bible Keeper conflict resolution | Two cases: review the case, or abide by programmer and work around it. Team vote on which path. | `[DECISION]` |
| 3:40 | Bible tag approval | Whole team approves tags, not just Fullstack. Agreed? | `[CONFIRMED]` |

---

## Part 7 — Verbose Task Breakdown & Naming (10 min)

| Time | Topic | Question | Decision target |
|------|-------|----------|-----------------|
| 3:50 | Week 1 deliverables | Each member (you, Miguel, Rui, Wakin) states one deliverable for each of the next 5 days. | `[CONFIRMED]` |
| 3:55 | Task IDs | Agree on naming convention: `FS-` (you), `PM-` (Miguel), `FE-` (Rui), `BD-` (Wakin). | `[CONFIRMED]` |
| 4:00 | Hour estimates | Are the estimates in the planning doc accurate, or do members renegotiate? | `[CONFIRMED]` |

---

## Decision log format
```
[DECISION/OPEN/CONFIRMED] Topic — <short statement> (agreed HH:MM)
Example: [DECISION] Bootstrap wins for V1 frontend framework (2:35)
```

All decisions must be written into the Bible Center immediately after the meeting.
