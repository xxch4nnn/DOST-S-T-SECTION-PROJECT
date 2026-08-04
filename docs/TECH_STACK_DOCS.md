# DOSTorage V1 — Tech stack docs (version-pinned)

**Purpose:** Anchor every agentic coding session to the **exact major/minor docs** that match lockfiles in this repo. Do not invent APIs from older major versions (e.g. Laravel 10/11, Livewire 2/3, Spatie permission v5).

**Authority order (highest first):**
1. This file + `composer.lock` / `package-lock.json` / CI workflow pins
2. Official docs linked below (version path in the URL)
3. Project conventions in `CONTRIBUTING.md` / `AGENTS.md`
4. Planning notes (may lag versions)

**How to re-pin after upgrades:** Update the “Locked” column from lockfiles, then fix doc URLs if the major line changed. Record the bump in `CHANGELOG.md` and `planning/AGENTIC_CHANGELOG.md`.

**Last verified:** 2026-08-05 against `origin/master` locks.

---

## Runtime / platform

| Component | Locked / CI pin | Official docs (use these) |
|-----------|-----------------|---------------------------|
| PHP | **8.3.6** local WAMP; CI `php-version: '8.3'`; Docker `php:8.3-fpm`; Composer `^8.3` | [PHP 8.3 release](https://www.php.net/releases/8.3/en.php) · [PHP manual](https://www.php.net/manual/en/) |
| Composer | 2.x (host / Docker image) | [Composer docs](https://getcomposer.org/doc/) |
| Node.js | CI **22** (`.github/workflows/test.yml`) | [Node.js 22.x API](https://nodejs.org/docs/latest-v22.x/api/) |
| MySQL | CI **`mysql:8.4`** | [MySQL 8.4 reference](https://dev.mysql.com/doc/refman/8.4/en/) |
| SQLite | Cloud/agent default (`DB_CONNECTION=sqlite`) | [SQLite docs](https://www.sqlite.org/docs.html) |
| Docker | App `Dockerfile` (`php:8.3-fpm` + nginx) | [Docker docs](https://docs.docker.com/) · [PHP Docker images](https://hub.docker.com/_/php) |

---

## Backend (Composer — from `composer.lock`)

| Package | Locked version | Constraint (`composer.json`) | Official docs |
|---------|----------------|------------------------------|---------------|
| laravel/framework | **v13.23.0** | `^13.8` | [Laravel 13.x docs](https://laravel.com/docs/13.x) · [Blade](https://laravel.com/docs/13.x/blade) · [Eloquent](https://laravel.com/docs/13.x/eloquent) · [Migrations](https://laravel.com/docs/13.x/migrations) · [Validation](https://laravel.com/docs/13.x/validation) · [Authorization](https://laravel.com/docs/13.x/authorization) · [Middleware](https://laravel.com/docs/13.x/middleware) · [Vite](https://laravel.com/docs/13.x/vite) · [Testing](https://laravel.com/docs/13.x/testing) · [Agentic development](https://laravel.com/docs/13.x/ai) |
| livewire/livewire | **v4.3.4** | `^4.3.3` | [Livewire 4.x docs](https://livewire.laravel.com/docs/4.x/quickstart) · [Components](https://livewire.laravel.com/docs/4.x/components) · [Properties](https://livewire.laravel.com/docs/4.x/properties) · [Actions](https://livewire.laravel.com/docs/4.x/actions) · [Forms](https://livewire.laravel.com/docs/4.x/forms) · [Validation](https://livewire.laravel.com/docs/4.x/validation) |
| livewire/volt | **v1.11.1** | `^1.7.0` | [Volt docs](https://livewire.laravel.com/docs/volt) (Volt is Livewire’s class-based single-file API — confirm against installed `v1.11.x`) |
| spatie/laravel-permission | **8.3.0** | `^8.3` | [Spatie Permission v8](https://spatie.be/docs/laravel-permission/v8/introduction) · [Middleware](https://spatie.be/docs/laravel-permission/v8/basic-usage/middleware) · [Policies](https://spatie.be/docs/laravel-permission/v8/best-practices/using-policies) · [Cache](https://spatie.be/docs/laravel-permission/v8/advanced-usage/cache) · [Seeding](https://spatie.be/docs/laravel-permission/v8/advanced-usage/seeding) |
| phpunit/phpunit | **12.5.31** | `^12.5.12` | [PHPUnit 12.5 docs](https://docs.phpunit.de/en/12.5/) |
| laravel/pint | **v1.30.2** | `^1.27` | [Laravel Pint](https://laravel.com/docs/13.x/pint) |
| laravel/breeze | **v2.4.2** | `^2.4` | [Breeze / starter kits](https://laravel.com/docs/13.x/starter-kits) |
| laravel/sail | **v1.64.0** | `^1.63` | [Laravel Sail](https://laravel.com/docs/13.x/sail) |

Project Spatie baseline (roles matrix, not upstream): `planning/SPATIE_ROLES_BASELINE.md`.

---

## Frontend (npm — from `package-lock.json`)

| Package | Locked version | Constraint (`package.json`) | Official docs |
|---------|----------------|------------------------------|---------------|
| bootstrap | **5.3.8** | `^5.3.3` | [Bootstrap 5.3 docs](https://getbootstrap.com/docs/5.3/getting-started/introduction/) · [Forms](https://getbootstrap.com/docs/5.3/forms/overview/) · [Components](https://getbootstrap.com/docs/5.3/components/buttons/) |
| vite | **8.2.0** | `^8.2.0` | [Vite guide](https://vite.dev/guide/) · [Vite 8 announcements / config](https://vite.dev/config/) |
| laravel-vite-plugin | **3.1.3** | `^3.1` | [Laravel Vite (13.x)](https://laravel.com/docs/13.x/vite) · [plugin README](https://github.com/laravel/vite-plugin) |
| sass | **1.102.0** | `^1.102.0` | [Sass documentation](https://sass-lang.com/documentation/) |
| axios | **1.19.0** | `^1.19.0` | [Axios docs](https://axios-http.com/docs/intro) |
| concurrently | **9.2.4** | `^9.0.1` | [concurrently (npm)](https://www.npmjs.com/package/concurrently) — **do not assume v10 APIs** while #31 is held |
| @popperjs/core | (Bootstrap peer) | `^2.11.8` | [Popper docs](https://popper.js.org/docs/v2/) |
| stylelint | (see lock) | `^17.14.1` | [Stylelint docs](https://stylelint.io/user-guide/) |

---

## Agentic coding rules (mandatory)

1. **Before implementing** a feature against a library, open the **versioned** official doc page above (not a blog, not Stack Overflow as SoT).
2. Prefer Laravel **13.x** URLs (`/docs/13.x/...`), Livewire **4.x** (`/docs/4.x/...`), Spatie Permission **v8**, Bootstrap **5.3**, PHPUnit **12.5**.
3. If lockfile version and docs major disagree after a Dependabot merge, **update this file in the same PR** as the bump.
4. Never “upgrade by coding” to a newer major (e.g. Livewire 5 APIs, Bootstrap 6) without an explicit dependency PR.
5. When unsure of an API, cite the doc URL in the PR description or AGENTIC changelog Linked field.

### Refresh commands

```bash
# Composer locked versions
php -r "echo json_encode(json_decode(file_get_contents('composer.lock'), true)['packages'][0] ?? 'use composer show');"
composer show laravel/framework livewire/livewire livewire/volt spatie/laravel-permission phpunit/phpunit --format=json

# npm locked versions
npm ls bootstrap vite laravel-vite-plugin sass axios concurrently --depth=0
```

---

## Related project docs

- Conventions: [`CONTRIBUTING.md`](../CONTRIBUTING.md)
- Agent workflow: [`AGENTS.md`](../AGENTS.md)
- Spatie roles in-app: [`planning/SPATIE_ROLES_BASELINE.md`](../planning/SPATIE_ROLES_BASELINE.md)
- Stitch plan: [`planning/STITCH_IMPLEMENTATION_PLAN.md`](../planning/STITCH_IMPLEMENTATION_PLAN.md)
