# DOSTorage V1 — Weekly Accomplishment Report

**Week #:** 1  
**Date Range:** July 13 – July 17, 2026  
**Programmer:** Chan  
**Assigned Role:** Fullstack / AIOps  
**Supervisor:** Ms. Hazel D. Prevendido  
**Agency:** Department of Science and Technology – Region XI (DOST RXI)  
**Intern Batch:** USeP BSCS OJT Team

---

## How I Set the Team Up

The first week was less about writing features and more about making sure the four of us could build this system without getting in each other’s way. I spent a big chunk of my time on infrastructure, automation, and the project’s “rules of the road” — things that would slow us down later if we left them to chance.

### Repository and Environment

- Scoped out the application skeleton and installed Laravel into the project root so everyone was working on the same baseline.
- Set up Docker Compose with MySQL 8.4, giving us a reproducible local stack instead of everyone manually installing WAMP/DBngin/TablePlus. The idea was to make onboarding painless; ideally a `docker compose up` is all anyone needs.
- Got the basics of the Git workflow right: `.gitignore`, repo structure, commit discipline, and a pull-request checklist.
- Studied the Laravel tech stack and related tooling so I could understand where the frontend, backend, and Livewire boundaries sit.

### CI/CD and Automation

- Created a GitHub Actions pipeline aimed at automating our teammate workflow checks — linting, tests, Bible Keeper audits. It wasn’t production-ready yet, but it gave us a visible target and a repeatable template.
- Built a small suite of helper scripts to handle the Gantt chart exports and schedule generation. These were tedious to do by hand in Google Docs, so automating them early meant we would not lose a day every time the plan changed.
- Began grooming the idea of an “always-on” Project Bible automation: first a standalone `bible_keeper` script, then a watcher daemon concept so our task list in the Bible Center stays synced with our local planning artifacts.

### Project Bible and Documentation Hygiene

- Helped design the Project Bible structure that the other teammates filled in: modules, flowcharts, schema drafts, and the MLS for administrative files.
- Drafted the Tech Documentation and incorporated the low-fidelity prototype notes after the client and internship orientation meetings.
- Redesigned how we represent the timeline and hour pool so Miguel and I could track learning hours versus implementation hours cleanly.
- Spent time on the Bible Center exploring ways to keep the document source of truth without duplicate entries or “ghost” updates.

---

## What I Had to Learn

- PHP fundamentals: conditionals, loops, strings, array handling, input sanitization, and just enough OOP to feel comfortable inside Laravel’s model layer.
- The Laravel + Bootstrap + Livewire + MySQL 8.4 stack well enough to know who should touch which file and why.
- Local server behavior under XAMPP, Herd, DBngin, and TablePlus — not just which tool to open, but what each one actually owns in the system.
- The full OJT rhythm: how DOST structures the schedule, their office workflow, the hierarchy between the Scholarship Head and the IT personnel, and why avoiding a silent blocker would be the most important discipline of the internship.

---

## Key Meetings / Milestones I Participated In

- **DOST-led orientation (July 13):** Picked up the agency profile, dress code, safety protocols, working hours, and our system mandate — basically the “why” behind this whole internship.
- **Client/IT requirements clarification (July 13–15):** Listed the MVP modules with the Scholarship Head and IT personnel; got firmer direction on offline-first behavior, file storage, Laravel package choices, pagination rules, and pagination/record search requirements.
- **Internal standalone / team standup session (July 14–16):** Turned our MVP talk into actual timelines, per-intern tasking, and an hour pool. We went from “we have a list of things we could do” to “these are the assignments, this is the week, these are the deadlines.”
- **Long-range planning (July 16):** Finalized roles, schedule, and Gantt chart visibility. This is where the team’s labor pool and per-sprint ownership became real.

---

## Team-Wide Deliverables I Helped Produce

- Technical Documentation version 0.x, including module-level flowcharts.
- Low-fidelity prototype and feature inventory.
- Draft database schema supporting:
  - Scholar records with file placeholders, duplicate detection, and clearance tracking
  - Administrative files with separate search and custom type support
  - Downloadable reports and admin-managed file metadata
- Project Bible with pseudocode, ERD/DBMS notes, and CRUD stubs for core tables.
- Draft handoff and CI pipeline artifacts staged in `.github/`.

---

## What Murked Up and Had to Be Fixed

- **Bible-write drift:** Early tests of writing back to the Google Doc targeted the wrong tab; the Bible Keeper script began scratching its own itch by overwriting content in `Bible Center` instead of `TASKS DETECTED`. I diagnosed the exact limitation: without Apps Script scoping or a Sheets proxy, live writes from this Windows/Hermes client can’t be reliably scoped per tab, so I switched the safe path to export-only writes until that gets sorted.
- **CI pipeline assumptions:** Needed to be simplified. The first draft tested everything at once; version two should run only what the current hour budget supports.

---

## Looking Ahead

| Focus Area | Next Action |
|---|---|
| Docker probing & deploy prep | Verify `docker compose` start/stop, snapshot healthcheck script |
| Bible Keeper tab-safe writes | Decide between Apps Script proxy vs. Sheets buffer |
| CI pipeline | Reduce to core checks only; add build artifacts |
| Backend scaffold | Wait for Wakin to finalize migrations, then wire Docker volumes |
| UI/UX handoff | Coordinate with Miguel on low-fi → high-fi transition |

---

*Report prepared by the Fullstack / AIOps intern based on planning folder artifacts, teammate DTR summaries, and the live Project Bible.*  
*Note: all thinking in this report is mine; layout and wording crafted directly, not copied from any template.*
