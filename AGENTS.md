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

## Cursor Cloud specific instructions

The cloud VM runs this Laravel 13 / Livewire 4 app natively (no Docker; `docker` is
not installed). PHP 8.3 + Composer and Node 22 are present. The startup update
script only refreshes dependencies (`composer install`, `npm install`); everything
below is NOT run automatically and must be done manually in a fresh workspace.

- Use SQLite for local dev (matches `.env.example` default `DB_CONNECTION=sqlite`
  and `phpunit.xml`). The Docker/CI MySQL path is not needed here.
- First-time setup after deps are installed (env, key, DB are not created by the
  update script and are gitignored, so re-run these if missing):
  - `cp .env.example .env`
  - `touch database/database.sqlite`
  - `php artisan key:generate`
  - `php artisan migrate --seed`  (seeds a Super Admin `test@example.com` / `password`)
- Build assets before testing or serving: `npm run build`. Non-obvious gotcha:
  the feature tests render Blade layouts that require `public/build/manifest.json`,
  so tests FAIL with "Vite manifest not found" if you skip the build (or a running
  `npm run dev`). The Bootstrap Sass deprecation warnings during build are harmless.
- Run the app in dev mode with two processes: `php artisan serve --host=0.0.0.0 --port=8000`
  and `npm run dev` (Vite on 5173). `composer dev` runs the full concurrently stack
  (server + queue + pail + vite) but pail/queue are optional for basic dev.
- Tests: `php artisan test` (do NOT pass `--no-interaction`; it is forwarded to
  PHPUnit which rejects it). Test output is emitted as a single JSON line.
- Lint: `vendor/bin/pint --test`. The existing codebase currently has many
  pre-existing Pint style violations, so this reports failures on a clean checkout;
  `vendor/bin/pint` (no `--test`) would auto-fix them.
- Seeded login for manual testing: `test@example.com` / `password` (Super Admin,
  email pre-verified).
