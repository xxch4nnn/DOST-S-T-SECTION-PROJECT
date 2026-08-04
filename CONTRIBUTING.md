# DOSTorage V1 — Coding Standards

## Stack
- Laravel **13.x** · Livewire **4.x** · Volt · Spatie Permission **v8** · Bootstrap **5.3** · Vite **8** · MySQL **8.4** (CI) · PHP **8.3** · Docker
- **Version-pinned official documentation:** [`docs/TECH_STACK_DOCS.md`](docs/TECH_STACK_DOCS.md) — agents and humans must use those URLs (not unversioned “latest” guides).
- Re-pin that file whenever `composer.lock` / `package-lock.json` / CI runtime versions change.

## Git workflow
- Branch naming: `feat/<task-id>-<short-desc>` and `fix/<task-id>-<short-desc>`
- Commit format: `<type>(<scope>): <message>`
- Types: `feat`, `fix`, `docs`, `refactor`, `test`, `chore`
- PRs: squash merge only; title should include task/issue ID when applicable
- **CODEOWNER review:** at least one approval from `CODEOWNERS` (`@xxch4nnn`, `@WakenMac`, `@Mushimuche`) before merge
- **Hold-release:** hold majors, failed smoke, missing changelog identity fields, or missing `docs/TECH_STACK_DOCS.md` re-pin after a version bump; release when CI green + CODEOWNER approval + required smoke/docs

## Dependabot
- **Minor/patch:** Path A preferred — smoke (`npm install` / `npm run build` / `npm run dev`; PHP: `composer install` + tests) → CODEOWNER approve → squash-merge.
- **Major bumps:** hold by default until explicit smoke + tech-stack doc re-pin.
- **Path B:** close if smoke fails; Dependabot may reopen after lockfile correction.
- **Path C (rare):** smoke clean but review blocked → CODEOWNER admin merge only with Chan/`@xxch4nnn` explicit approval.
- Same-PR rule: lockfile bump → update `docs/TECH_STACK_DOCS.md` URLs/locked versions (follow-up PR immediately after Dependabot if needed).

## Code review checklist
1. Tests pass: `php artisan test`
2. Lint clean: no new warnings
3. No hardcoded secrets or `.env` leaks
4. Migration rollback-safe: `php artisan migrate:rollback` works
5. README/docs updated if behavior changed
6. Changelog bullets include date-time (+08:00) and user
7. Dependency bumps update `docs/TECH_STACK_DOCS.md` when locked versions change

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

## Changelog (required)
This project uses three complementary changelog mechanisms. All are mandatory for the respective actors; skipping any of them is a review blocker.

### Product/behavior changelog
- Update root [`CHANGELOG.md`](CHANGELOG.md) under `[Unreleased]` for any PR that changes behavior, schema, seeders, public UI, or CI.
- Use conventional sections: `Added` / `Changed` / `Deprecated` / `Removed` / `Fixed` / `Security`.
- **Every bullet must include date-time and user** (copy this shape):

```text
- **2026-08-05 00:30:00 +08:00** · **Chan** (`@xxch4nnn`) — Short user-facing summary (#NN).
```

  Default timezone Asia/Manila (`+08:00`). Agents list the human principal first.

### Agent/ops trail
- Append [`planning/AGENTIC_CHANGELOG.md`](planning/AGENTIC_CHANGELOG.md) for investigations, pins of external repos, stitch ports, audits, and handoff artifact creation.
- Required fields: **Date**, **Time** (`HH:mm:ss +08:00`), **User** (human), **Actor** (executor/agent), Repo + branch, Action, Commit/PR, Summary, Linked artifact.

```text
- **Date:** 2026-08-05
- **Time:** 00:30:00 +08:00
- **User:** Chan (`@xxch4nnn`)
- **Actor:** Composer / Chan
- **Repo:** `xxch4nnn/DOST-S-T-SECTION-PROJECT` @ `branch-name`
- **Action:** docs update
- **Commit/PR:** #NN → `abc1234`
- **Summary:** One-line what/why.
- **Linked:** `path/to/artifact`
```

### Stitch execution log
- While running backend-to-mother stitch work, also append [`planning/STITCH_EXECUTION_LOG.md`](planning/STITCH_EXECUTION_LOG.md).

### Review enforcement
- Reviewers must request a changelog amendment before squash-merge if a behavior-changing PR omits one of the above.
- Canonical stitch plan: [`planning/STITCH_IMPLEMENTATION_PLAN.md`](planning/STITCH_IMPLEMENTATION_PLAN.md).
