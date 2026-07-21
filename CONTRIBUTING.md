# DOSTorage V1 — Coding Standards

## Stack
- Laravel
- Livewire
- Bootstrap
- Spatie permissions
- MySQL
- Docker

## Git workflow
- Branch naming: `feat/<task-id>-<short-desc>` and `fix/<task-id>-<short-desc>`
- Commit format: `<type>(<scope>): <message>`
- Types: `feat`, `fix`, `docs`, `refactor`, `test`, `chore`
- PRs require 1 reviewer, title must include task ID, squash merge only

## Code review checklist
1. Tests pass: `php artisan test`
2. Lint clean: no new warnings
3. No hardcoded secrets or `.env` leaks
4. Migration rollback-safe: `php artisan migrate:rollback` works
5. README/docs updated if behavior changed

## PHP/Laravel conventions
- Controllers: PascalCase, e.g. `DocumentController`, `ScholarController`
- Blade files: lowercase, e.g. `scholars/index.blade.php`
- Named routes over raw URLs, e.g. `scholars.index`, `scholars.destroy`
- Livewire components must follow exact `make:livewire` naming; mismatches break components silently
- Prefer Eloquent over raw SQL to reduce injection risk

## Database/migration rules
- Do not add destructive schema changes without rollback plan
- Keep lookup tables data-driven for routine additions
- No hard deletes in V1; use soft-delete or strike-off flow only

## Testing
- Add tests with implementation work
- Minimum set: auth, CRUD, upload validation, role gates, strike-off/restore
- Feature tests live in `tests/Feature/`
- Use factories and seeders for repeatable data

## Secrets and environment
- Never commit `.env`, API keys, or local-only credentials
- Use `.env.example` for required variables only
- Review diffs for accidental secret leakage before push

## Documentation
- Update `README.md` and planning docs when behavior changes
- Record client-facing decisions in the source-of-truth doc
- Leave pointers when moving or superseding artifacts
