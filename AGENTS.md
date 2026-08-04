# DOSTorage V1 — Project Rules

Use these rules whenever this repo is the working directory.

## Identity
- Project: DOST-SEI Davao Region Scholarship Records Management System (DOSTorage V1)
- Your role in this repo: Fullstack / AIOps assistant
- Principal dev / human owner: Chan
- Source of truth: Bible Center doc ID `1TL6YADi71bi9fHAaF8YAypZWW-jCpDGkQvJosera-Ms`

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
- Bible helper: `.\\scripts\\bible_keeper.bat`
- WAMP PHP 8.3.6: `C:\wamp64\bin\php\php8.3.6\php.exe`
- Composer: `C:\wamp64\bin\php\php8.3.6\composer.bat`
- Python 3.11: `C:\Users\Asus\AppData\Local\Programs\Python\Python311\python.exe`
- Google token: `C:\Users\Asus\AppData\Local\hermes\google_token.json`

## Project conventions
- Use fullstack/AIOps lens: Docker, CI, deploys, automation, infrastructure, repo hygiene
- Do not build backend models/migrations unless explicitly asked
- Prefer modifying planning/docs artifacts inside `planning\`
- Keep Bible Center clean: archive resolved items, avoid duplicate findings
- Do **not** use `planning/TASKS_DETECTED_payload.md` for current status (retired 2026-08-04); use checklist + `planning/team_*.csv`
- Chan handoff actions: `planning/HANDOFF_GUIDE.md`; cross-role work: `planning/HANDOFF_GITHUB_ISSUES.md`

## Tech stack documentation (mandatory for agents)
- Before implementing against any library in this repo, open the **version-pinned official docs** in [`docs/TECH_STACK_DOCS.md`](docs/TECH_STACK_DOCS.md).
- Authority: lockfiles (`composer.lock`, `package-lock.json`, CI pins) → that file’s URLs → `CONTRIBUTING.md` conventions.
- Do not code against older major docs (Laravel 10/11, Livewire 2/3, Spatie Permission v5–v7, Bootstrap 4).
- After Dependabot/manual upgrades, update `docs/TECH_STACK_DOCS.md` in the same PR.
- Cursor always-on rule: `.cursor/rules/tech-stack-docs.mdc`

## Changelog (mandatory for agents)
- After any behavior/schema/seeder/CI change, update root `CHANGELOG.md` `[Unreleased]` with **date-time (+08:00)** and **user** on every bullet (see format in that file).
- After any investigation, external-repo pin, or stitch port session, append `planning/AGENTIC_CHANGELOG.md` with **Date**, **Time**, **User**, Actor, Repo@branch, Action, Commit/PR, Summary, Linked.
- During backend-to-mother stitch execution, also append `planning/STITCH_EXECUTION_LOG.md` (include Date + time + Actor).
- Do not skip changelog updates “until later”; treat them as part of the same turn as the code/docs change.
- Stitch SoT plan: `planning/STITCH_IMPLEMENTATION_PLAN.md` (supersedes ad-hoc Antigravity/AGY drafts when they conflict — prefer additive migrations).

## Cursor Cloud specific instructions

The Cloud VM runs this Laravel 13 / Livewire 4 app **natively with SQLite**. Docker is not installed on the Cloud VM (local Windows/WAMP and Docker/CI MySQL remain separate paths). PHP 8.3 + Composer and Node 22 are present. The startup update script only refreshes dependencies (`composer install`, `npm install`); everything below is NOT run automatically and must be done manually in a fresh workspace.

- Use SQLite for Cloud/local agent dev (matches `.env.example` default `DB_CONNECTION=sqlite` and `phpunit.xml`). CI uses MySQL; you do not need MySQL on the Cloud VM.
- First-time setup after deps are installed (env, key, DB are not created by the update script and are gitignored, so re-run these if missing):
  - `cp .env.example .env`
  - `touch database/database.sqlite`
  - `php artisan key:generate`
  - `php artisan migrate --seed`  (seeds a Super Admin `test@example.com` / `password`)
- Build assets before **serving the UI**: `npm run build` (or keep `npm run dev` running). Bootstrap Sass deprecation warnings during build are harmless. Feature tests call `withoutVite()` in `tests/TestCase.php`, so they do **not** require `public/build/manifest.json`.
- Run the app in dev mode with two processes: `php artisan serve --host=0.0.0.0 --port=8000` and `npm run dev` (Vite on 5173). `composer dev` runs the full concurrently stack (server + queue + pail + vite) but pail/queue are optional for basic dev.
- Tests: `php artisan test` (do NOT pass `--no-interaction`; PHPUnit rejects it).
- Lint PHP: `vendor/bin/pint --test` (clean on current `master`). Lint SCSS: `npm run lint:css` (CI job `lint-css`).
- Seeded login for manual testing: `test@example.com` / `password` (Super Admin, email pre-verified; seed-only credentials).
