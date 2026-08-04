# DOSTorage V1 — Project Rules

Use these rules whenever this repo is the working directory.

## Identity
- Project: DOST-SEI Davao Region Scholarship Records Management System (DOSTorage V1)
- Your role in this repo: Fullstack / AIOps assistant
- Principal dev / human owner: Chan (`@xxch4nnn`)
- Source of truth: Bible Center doc ID `1cmgb3y807KI5RSJABx93bC3PcI3rRFLpMBzCHg9x8xw` (migrated; do not use old ID `1TL6YADi71bi9fHAaF8YAypZWW-jCpDGkQvJosera-Ms`)

## Daily assistant behavior
- Standup rhythm: morning time record update, mini meeting, work session, standup at 5:00 PM
- Bible Keeper is the clone/archive-aware scanner; do not duplicate Open Floor entries
- Do not edit `TEAM_WORKFLOW.md` directly; produce review-ready suggestions only
- If the user asks for a council review, spawn a leaf subagent and return a recommend/revise/reject verdict
- Outputs should be visually polished and organized without asking confirmation on layout
- Prefer simple, understandable artifacts over standalone complex tools

## Hard boundaries
- Do not push client-facing artifacts unsupervised; route through PM/UI-UX owner
- Do not change team pool/hour tracking models

## Windows / local environment
- Working dir: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage`
- Planning dir: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning`
- Bible helper: `cmd.exe /c scripts\bible_keeper.bat` (always invoke `.bat` via `cmd.exe /c`, not bash)
- WAMP PHP 8.3.6: `C:\wamp64\bin\php\php8.3.6\php.exe`
- Composer: `cmd.exe /c C:\wamp64\bin\php\php8.3.6\composer.bat`
- Python 3.11: `C:\Users\Asus\AppData\Local\Programs\Python\Python311\python.exe`
- Google token: `C:\Users\Asus\AppData\Local\hermes\google_token.json`

### Shell conventions (Windows)
| Task | Shell | Notes |
|------|-------|-------|
| `find` / `grep` / POSIX tools | **Git Bash** | Prefer bash for ripgrep-style searches when not using Cursor Grep |
| `.bat` / `.cmd` scripts | **`cmd.exe /c …`** | Do not run `.bat` through PowerShell call operator alone when paths need cmd parsing |
| Env vars, process control, path joins | **PowerShell** | `$env:Path`, `Stop-Process`, `Join-Path` |
| PHP / Artisan / Composer / npm | PowerShell or cmd with WAMP PHP on `PATH` | Prefer WAMP PHP 8.3.6 over system PHP |

## Project conventions
- Use fullstack/AIOps lens: Docker, CI, deploys, automation, infrastructure, repo hygiene
- Do not build backend models/migrations unless explicitly asked
- Prefer modifying planning/docs artifacts inside `planning\`
- Keep Bible Center clean: archive resolved items, avoid duplicate findings
- Do **not** use `planning/TASKS_DETECTED_payload.md` for current status (retired 2026-08-04); use checklist + `planning/team_*.csv`

## Handoff artifact ownership
| Owner | Scope | Canonical paths |
|-------|--------|-----------------|
| **Chan** (`@xxch4nnn`) | Chan-only actions, principal checklists, DTR/time, Bible Keeper runs, mother-repo merge authority | `planning/HANDOFF_GUIDE.md` |
| **Non-Chan** (Wakin, Miguel, Rui, Hermes, agents) | Cross-role issues, QA evidence asks, backend/UI handoffs filed as GitHub issues | `planning/HANDOFF_GITHUB_ISSUES.md`, `planning/HANDOFF_COVERAGE_MATRIX.md` |
- Agents authoring handoff docs: put Chan-scoped work in `HANDOFF_GUIDE.md`; put multi-person / issue-tracked work in `HANDOFF_GITHUB_ISSUES.md`. Do not invent a third handoff SoT.

## Tech stack documentation (mandatory for agents)
- Before implementing against any library in this repo, open the **version-pinned official docs** in [`docs/TECH_STACK_DOCS.md`](docs/TECH_STACK_DOCS.md).
- **Authority order (highest first):** lockfiles (`composer.lock`, `package-lock.json`, CI pins) → doc URLs in `docs/TECH_STACK_DOCS.md` → `CONTRIBUTING.md` / this file.
- Do not code against older major docs (Laravel 10/11, Livewire 2/3, Spatie Permission v5–v7, Bootstrap 4).
- After Dependabot/manual upgrades, **update `docs/TECH_STACK_DOCS.md` in the same PR** as the lockfile bump (or immediately after squash-merge if Dependabot cannot edit docs).
- Cursor always-on rule: `.cursor/rules/tech-stack-docs.mdc`

## Dependabot policy
- **Minor/patch:** prefer Path A — local smoke (`npm install`, `npm run build`, `npm run dev`; for PHP bumps also `composer install` + `php artisan test`) then CODEOWNER approve + squash-merge when CI is green.
- **Major bumps:** **hold by default**. Do not auto-merge. Require explicit smoke (Path A) against the major line and a `docs/TECH_STACK_DOCS.md` re-pin in the same follow-up PR.
- **Paths:**
  - **A (preferred):** Smoke clean → approve → squash-merge.
  - **B (blocker):** Smoke fails → close PR; let Dependabot reopen after lockfile correction.
  - **C (rare):** Smoke clean but review gate blocked → CODEOWNER approve + admin merge only with explicit principal approval.
- Known Windows note: `composer dev` may fail on `pail`/`pcntl`; that alone is not a smoke failure for npm tool bumps if Vite/build/server otherwise start.

## PR review / CODEOWNER hold-release
- `CODEOWNERS` require review from `@xxch4nnn`, `@WakenMac`, or `@Mushimuche`.
- Squash-merge only; PR title should include task/issue ID when applicable.
- **Hold** majors, broken smoke, missing changelog identity fields, or missing tech-stack doc re-pin after a version bump.
- **Release** when CI green, required smoke done (majors), at least one CODEOWNER approval, and changelogs/docs updated.
- Path C admin merge requires Chan/`@xxch4nnn` explicit go-ahead in-session.

## Changelog (mandatory for agents)
- After any behavior/schema/seeder/CI change, update root `CHANGELOG.md` `[Unreleased]` with **date-time (+08:00)** and **user** on every bullet.
- After any investigation, external-repo pin, or stitch port session, append `planning/AGENTIC_CHANGELOG.md` with **Date**, **Time**, **User**, Actor, Repo@branch, Action, Commit/PR, Summary, Linked.
- During backend-to-mother stitch execution, also append `planning/STITCH_EXECUTION_LOG.md` (include Date + time + Actor).
- Do not skip changelog updates “until later”; treat them as part of the same turn as the code/docs change.
- Stitch SoT plan: `planning/STITCH_IMPLEMENTATION_PLAN.md` (supersedes ad-hoc Antigravity/AGY drafts when they conflict — prefer additive migrations).
- Cursor rule: `.cursor/rules/changelog-datetime-user.mdc`

### Changelog example (copy this shape)

```text
### Changed
- **2026-08-05 00:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Bump concurrently to 10.0.4 after smoke; re-pin TECH_STACK_DOCS (#31).
```

```text
## 2026-08-05
- **Date:** 2026-08-05
- **Time:** 00:30:00 +08:00
- **User:** Chan (`@xxch4nnn`)
- **Actor:** Composer / Chan
- **Repo:** `xxch4nnn/DOST-S-T-SECTION-PROJECT` @ `chore/agents-improvements-and-concurrently`
- **Action:** docs update + Dependabot merge
- **Commit/PR:** #31 → `fdba7d8`
- **Summary:** Smoke-approved concurrently 10; AGENTS Dependabot/CODEOWNER/Bible pointer updates.
- **Linked:** `AGENTS.md`, `docs/TECH_STACK_DOCS.md`
```

## Cursor Cloud specific instructions

The Cloud VM runs this Laravel 13 / Livewire 4 app **natively with SQLite**. Docker is not installed on the Cloud VM (local Windows/WAMP and Docker/CI MySQL remain separate paths). PHP 8.3 + Composer and Node 22 are present. The startup update script only refreshes dependencies (`composer install`, `npm install`); everything below is NOT run automatically and must be done manually in a fresh workspace.

- Use SQLite for Cloud/local agent dev (matches `.env.example` default `DB_CONNECTION=sqlite` and `phpunit.xml`). CI uses MySQL; you do not need MySQL on the Cloud VM.
- First-time setup after deps are installed (env, key, DB are not created by the update script and are gitignored, so re-run these if missing):
  - `cp .env.example .env`
  - `touch database/database.sqlite`
  - `php artisan key:generate`
  - `php artisan migrate --seed`  (seeds a Super Admin `test@example.com` / `password`)
- Build assets before **serving the UI**: `npm run build` (or keep `npm run dev` running). Bootstrap Sass deprecation warnings during build are harmless. Feature tests call `withoutVite()` in `tests/TestCase.php`, so they do **not** require `public/build/manifest.json`.
- Run the app in dev mode with two processes: `php artisan serve --host=0.0.0.0 --port=8000` and `npm run dev` (Vite on 5173). `composer dev` runs the full concurrently stack (server + queue + pail + vite) but pail/queue are optional for basic dev (Pail needs `pcntl`, often missing on Windows WAMP).
- Tests: `php artisan test` (do NOT pass `--no-interaction`; PHPUnit rejects it).
- Lint PHP: `vendor/bin/pint --test` (clean on current `master`). Lint SCSS: `npm run lint:css` (CI job `lint-css`).
- Seeded login for manual testing: `test@example.com` / `password` (Super Admin, email pre-verified; seed-only credentials).
