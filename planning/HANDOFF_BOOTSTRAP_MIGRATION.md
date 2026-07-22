# Task Handoff — Tailwind 3 → Bootstrap 5 Migration

## Assignee
Frontend team member (Rui or designated implementer)

## Deadline
By **5:00 PM today** — Bootstrap 5 runtime active, all views render, backend tests still pass

## Working Directory
`C:\Users\Asus\Documents\Personal\Programs\DOSTorage`

## Hard Contract
- Do not change backend PHP classes, routes, migrations, or Docker files
- Do not push client-facing artifacts unsupervised
- Do not edit `TEAM_WORKFLOW.md`
- You may edit Blade views, package.json, Vite config, JS/CSS entrypoints
- Commit in small logical chunks

---







# Bootstrap 5 Migration RuleSet

## 1. Objective
Eliminate every trace of Tailwind 3 and leave only Bootstrap 5. This means:
- `package.json` has no `tailwindcss`, `@tailwindcss/*`, `autoprefixer`, or `postcss`
- `resources/css/app.scss` imports Bootstrap + custom styles only
- All 39 Blade files use Bootstrap classes only
- All Tailwind-specific classes (`bg-*`, `text-*`, `p-*`, `m-*`, `flex`, `grid`, `rounded-*`, `shadow-*`, etc.) are gone

## 2. Verified Current State

| Layer | Current | Notes |
|---|---|---|
| CSS Framework | Tailwind 3.1.0 + `@tailwindcss/forms` | No Bootstrap installed |
| PostCSS pipeline | `tailwindcss` + `autoprefixer` | `postcss.config.js` present |
| Vite input | `resources/css/app.css`, `resources/js/app.js` | `vite.config.js` references these |
| CSS source | `resources/css/app.css` has `@tailwind base/components/utilities` | Must be replaced |
| `resources/js/app.js` | `import 'bootstrap'` then `import './bootstrap.js'` | Stale/incorrect; replace |
| `resources/js/bootstrap.js` | Does not appear to exist or is corrupted | Recreate |
| Blade views using Tailwind | ~30 of 39 files | Key files: layouts, components, all Livewire views |
| Tailwind config | `tailwind.config.js` present | Delete |

## 3. Tooling Removed vs Added

### Remove exactly these npm packages (from host-side `package.json`)
- `tailwindcss`
- `@tailwindcss/forms`
- `@tailwindcss/vite`
- `autoprefixer`
- `postcss`

### Add exactly these npm packages
- `bootstrap` (5.x)
- `sass` (for SCSS compilation through Vite)
- `@popperjs/core` (Bootstrap dropdowns/modals)

## 4. npm Package Script Maintained
Keep existing scripts:
```json
"build": "vite build",
"dev": "vite"
```

## 5. Step-by-Step Execution

### Step 1 — Remove Tailwind from `package.json`

Edit `package.json` `devDependencies` to remove:
- `@tailwindcss/forms`
- `@tailwindcss/vite`
- `autoprefixer`
- `postcss`
- `tailwindcss`

Add:
- `bootstrap`
- `sass`
- `@popperjs/core`

Then:
```bash
rm -f tailwind.config.js postcss.config.js
```

### Step 2 — Update `vite.config.js`

Replace entire file with:
```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### Step 3 — Replace CSS entry with SCSS

Delete: `resources/css/app.css`

Create: `resources/css/app.scss`
```scss
// Bootstrap 5 imports
@import '~bootstrap/scss/functions';
@import '~bootstrap/scss/variables';
@import '~bootstrap/scss/variables-dark';
@import '~bootstrap/scss/maps';
@import '~bootstrap/scss/mixins';
@import '~bootstrap/scss/root';
@import '~bootstrap/scss/reboot';
@import '~bootstrap/scss/type';
@import '~bootstrap/scss/images';
@import '~bootstrap/scss/containers';
@import '~bootstrap/scss/grid';
@import '~bootstrap/scss/tables';
@import '~bootstrap/scss/forms';
@import '~bootstrap/scss/buttons';
@import '~bootstrap/scss/transitions';
@import '~bootstrap/scss/dropdown';
@import '~bootstrap/scss/spacer';
@import '~bootstrap/scss/nav';
@import '~bootstrap/scss/navbar';
@import '~bootstrap/scss/card';
@import '~bootstrap/scss/accordion';
@import '~bootstrap/scss/breadcrumb';
@import '~bootstrap/scss/pagination';
@import '~bootstrap/scss/badge';
@import '~bootstrap/scss/alert';
@import '~bootstrap/scss/progress';
@import '~bootstrap/scss/list-group';
@import '~bootstrap/scss/close';
@import '~bootstrap/scss/toasts';
@import '~bootstrap/scss/modal';
@import '~bootstrap/scss/tooltip';
@import '~bootstrap/scss/popover';
@import '~bootstrap/scss/carousel';
@import '~bootstrap/scss/offcanvas';
@import '~bootstrap/scss/placeholders';
@import '~bootstrap/scss/helpers';
@import '~bootstrap/scss/utilities';

// Custom overrides (add your Bootstrap variable overrides above the utilities import)
```

### Step 4 — Fix `resources/js/app.js`

Replace with:
```js
import 'bootstrap';
import './bootstrap.js';
```

Create/replace `resources/js/bootstrap.js`:
```js
import _ from 'lodash';
import 'bootstrap';

window._ = _;
```

Note: If `lodash` is not in `package.json`, install it: `npm install lodash --save-dev`

### Step 5 — Convert `package.json` (full valid example)

After edits, `devDependencies` in `package.json` should look roughly like:
```json
{
  "concurrently": "^9.0.1",
  "laravel-vite-plugin": "^3.1",
  "sass": "^1.77.0",
  "vite": "^8.1.5",
  "bootstrap": "^5.3.3",
  "@popperjs/core": "^2.11.8"
}
```

Do **not** include `tailwindcss`, `@tailwindcss/*`, `autoprefixer`, or `postcss`.

### Step 6 — Convert Layouts

#### `resources/views/layouts/app.blade.php`

Replace with:
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="min-vh-100 bg-light">
        <livewire:layout.navigation />
        @if (isset($header))
            <header class="bg-white shadow-sm">
                <div class="container mt-4 mb-5 p-4">
                    {{ $header }}
                </div>
            </header>
        @endif
        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
```

#### `resources/views/layouts/guest.blade.php`

Replace with:
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="text-body">
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="w-100 mx-auto mt-5 p-4 bg-white shadow-sm rounded" style="max-width: 28rem;">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
```

Remove the Figtree/Bunny font link from both layouts.

### Step 7 — Convert Shared Components

For each file in `resources/views/components/`, remove Tailwind classes and apply Bootstrap equivalents. Examples:

| File | Bootstrap replacement |
|---|---|
| `primary-button.blade.php` | `<button class="btn btn-primary">...</button>` |
| `secondary-button.blade.php` | `<button class="btn btn-secondary">...</button>` |
| `danger-button.blade.php` | `<button class="btn btn-danger">...</button>` |
| `text-input.blade.php` | `<input class="form-control" ...>` |
| `input-label.blade.php` | `<label class="form-label">...</label>` |
| `input-error.blade.php` | `<div class="invalid-feedback d-block">...</div>` |
| `dropdown.blade.php` | Use Bootstrap dropdown markup: `.dropdown`, `.dropdown-toggle`, `.dropdown-menu` |
| `dropdown-link.blade.php` | `<a class="dropdown-item" href="#">...</a>` |
| `modal.blade.php` | Bootstrap 5 modal markup + `data-bs-toggle="modal"` attributes |
| `nav-link.blade.php` | `<a class="nav-link" href="#">...</a>` |

Global rule: every class attribute must use Bootstrap classes only. No Tailwind utilities remain.

### Step 8 — Convert Livewire Views (19 files)

Replace all Tailwind utilities across:
- `resources/views/livewire/admin-records/*.blade.php` (create, edit, index, show)
- `resources/views/livewire/audit-logs/index.blade.php`
- `resources/views/livewire/layout/navigation.blade.php`
- `resources/views/livewire/pages/auth/*.blade.php` (confirm-password, forgot-password, login, register, reset-password, verify-email)
- `resources/views/livewire/profile/*.blade.php` (delete-user-form, update-password-form, update-profile-information-form)
- `resources/views/livewire/scholars/*.blade.php` (create, edit, index, show)

Conversion rules per view:
- Forms: replace `border-gray-300 focus:ring-indigo-500 ...` with `form-control`
- Buttons: replace `bg-indigo-600 hover:bg-indigo-700 text-white` with `btn btn-primary`
- Tables: replace `min-w-full divide-y` with `table table-striped table-hover`
- Cards: replace `bg-white shadow rounded-lg` with `card shadow-sm`
- Badges: replace `badge bg-green-100 text-green-800` with `badge bg-success`
- Alerts: `bg-red-50 text-red-800` → `alert alert-danger`
- Flex utilities: `flex items-center justify-between` → `d-flex align-items-center justify-content-between`
- Spacing: `p-4` → `p-3` (Bootstrap spacing scale), `mb-4` → `mb-3`
- Responsive columns: `md:w-1/2` → `col-md-6`

**Do not change any Livewire PHP logic or `wire:*` directives.**

### Step 9 — Convert Remaining Pages

- `resources/views/welcome.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/profile.blade.php`

Remove all Tailwind classes.

### Step 10 — Rebuild via Docker

```bash
# Rebuild image (if node_modules changed on host)
docker compose build

# Start stack
docker compose up -d

# Install PHP deps
docker compose exec app composer install --no-interaction --prefer-dist

# Install JS deps inside container
docker compose exec app npm install

# Clear caches
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear

# Build frontend assets via Vite inside container (if npm run dev/build exists)
docker compose exec app npm run build

# Smoke test HTTP
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000
```

---

# Verification Checklist

## Functional
- [ ] `http://localhost:8000` returns HTTP 200
- [ ] Login page renders with Bootstrap styling
- [ ] Scholar index page renders with Bootstrap table classes
- [ ] Admin records index renders
- [ ] Navigation shows and is functional
- [ ] Modals/dropdowns work (Bootstrap JS initialized)

## No Tailwind Residual
- [ ] `grep -r "bg-gray-\|text-gray-\|font-sans\|antialiased\|min-h-screen\|max-w-7xl\|sm:px-6\|lg:px-8\|@tailwind" resources/` returns nothing
- [ ] `grep -r "tailwindcss" package.json` returns nothing
- [ ] `ls tailwind.config.js postcss.config.js` returns nothing

## Builds Pass
- [ ] `npm run build` compiles SCSS without error
- [ ] `php artisan test` backend tests still pass (same pass/fail count as before migration)

## Git Hygiene
- [ ] No uncommitted Docker/runtime changes mixed in
- [ ] Commits are small and reviewable per section above

---

# Exact Backtrack Procedure

If bootstrap build fails or views break and cannot be fixed within 45 minutes:

```bash
# Restore package.json from HEAD
git show HEAD:package.json > package.json
git show HEAD:vite.config.js > vite.config.js

# Restore CSS
rm -f resources/css/app.scss
cat > resources/css/app.css << 'EOF'
@tailwind base;
@tailwind components;
@tailwind utilities;
EOF

# Restore JS
git show HEAD:resources/js/app.js > resources/js/app.js
rm -f resources/js/bootstrap.js

# Reinstall dependencies
docker compose exec app npm install

# Rebuild/restart
docker compose build
docker compose up -d

# Verify back to known good
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000
```

Then:
1. Note failure in `planning/DOCKER_RUNTIME_V1.md` Blockers
2. Report blocker in standup Blocked field
3. Do not leave repo with half-migrated CSS

---

# 5:00 PM Standup Output Template

| Section | Content |
|---|---|
| **Done** | Removed Tailwind deps; installed Bootstrap 5; updated Vite/SCSS pipeline; converted layouts/components/Livewire views; build green |
| **Blocked** | [List any views/components not yet converted] |
| **Next** | [e.g., Responsive regression testing; team review of Bootstrap theme] |

---

# Files That Must Not Be Changed

- `compose.yaml`
- `Dockerfile`
- `docker/nginx/default.conf`
- `database/migrations/*`
- `app/*` PHP classes
- `routes/*`
- `planning/long_range_planning.md`
- `.env`

Any change to these is out of scope and should be reported as a blocker.

---

# Bootstrap Class Mapping Quick Reference

| Removed Tailwind | Use This Bootstrap Instead |
|---|---|
| `font-sans antialiased` on body | Remove entirely (Bootstrap system fontstack) |
| `min-h-screen bg-gray-100` | `min-vh-100 bg-light` |
| `max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8` | `container mt-4 mb-5 p-4` |
| `bg-white shadow` | `bg-white shadow-sm` |
| `text-gray-900` | `text-body` |
| `text-gray-700` | `text-body` |
| `text-gray-500` | `text-muted` |
| `text-sm` | `small` |
| `font-medium` | `fw-medium` |
| `font-semibold` | `fw-semibold` |
| `rounded-lg` | `rounded` |
| `rounded-md` | `rounded-2` |
| `shadow-md` | `shadow` |
| `p-4` | `p-3` |
| `mb-4` | `mb-3` |
| `block` | `d-block` |
| `flex` | `d-flex` |
| `items-center` | `align-items-center` |
| `justify-center` | `justify-content-center` |
| `justify-between` | `justify-content-between` |
| `hidden` | `d-none` |
| `sr-only` | `visually-hidden` |
| `text-center` | `text-center` |
| `text-start` | `text-start` |
| `text-end` | `text-end` |
| `border` | `border` |
| `border-gray-300` | `border` |
| `focus:border-indigo-500 focus:ring-indigo-500` | `form-control` |
| `bg-red-600 hover:bg-red-700 text-white` | `btn btn-danger` |
| `bg-indigo-600 hover:bg-indigo-700 text-white` | `btn btn-primary` |
| `bg-gray-800 hover:bg-gray-700 text-white` | `btn btn-dark` |
| `badge bg-green-100 text-green-800` | `badge bg-success` |
| `alert-success` (Tailwind pattern) | `alert alert-success` |
| `table min-w-full divide-y` | `table table-striped table-hover` |

**Rule:** When in doubt, use Bootstrap’s built-in form/table/alert/nav classes first. Only add custom classes after confirming Bootstrap cannot do it with utilities.
