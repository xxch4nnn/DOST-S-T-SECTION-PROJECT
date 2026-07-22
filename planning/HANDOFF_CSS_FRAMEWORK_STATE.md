# Repo State — Tailwind vs Bootstrap / Hour 74

## Verified Current State

| Layer | Actual |
|---|---|
| **CSS framework present** | Tailwind 3.1.0, `@tailwindcss/forms`, `@tailwindcss/vite`, PostCSS config |
| **Bootstrap present** | **None** in `package.json` |
| **CSS entrypoint** | `resources/css/app.css` with `@tailwind base; @tailwind components; @tailwind utilities;` |
| **Vite input** | `resources/css/app.css`, `resources/js/app.js` |
| **Layout files** | `resources/views/layouts/app.blade.php`, `guest.blade.php` use Tailwind utilities |
| **Blade components** | 30–39 files use Tailwind classes |
| **Livewire views** | 19 files use Tailwind utilities |
| **README/checklists** | Document Bootstrap 5 |
| **Reality** | Tailwind only |

## Bootstrap Migration Scope

| Task | Scope |
|---|---|
| **Tooling** | Remove Tailwind/PostCSS deps; add `bootstrap@5`, `sass`, `@popperjs/core`; update Vite/SCSS entry |
| **Regenerate Tailwind files** | Can rollback if frontend gets unstable during migration |
| **CSS entry** | Replace with `resources/css/app.scss` importing Bootstrap SCSS |
| **Blade classes** | Rewrite ~30–39 files from Tailwind utilities to Bootstrap utilities/components |
| **Verify** | `npm run build` and `php artisan test` |

## Hard Constraint

- Do not change `package.json` levels of work completion evidence
- PowerShell edits are problematic, so this is should be treated as an executive handoff only

## Decision

| Requested | Plan Stated | Actual |
|---|---|---|
| Bootstrap 5 only | Bootstrap 5 | Tailwind 3 |

Required work:
1. Frontend engineer handoff to migrate Tailwind to Bootstrap consistently
2. Verify build + smoke test after migration
3. Do not touch PHP/routes/migrations/Docker for this task
