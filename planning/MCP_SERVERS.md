# DOSTorage V1 — MCP Server Registry

This file tracks candidate MCP servers for the project.  
Nothing here is auto-installed. Each entry stays **optional** until the team agrees to enable it during implementation.

## Usage rule
- Add entries here before enabling them in any local Hermes or IDE config.
- If an MCP server touches client data, repo code, or production config, it must be reviewed in standup first.

---

## Candidates

### 1. Laravel Boost
- **Category:** Framework / Laravel
- **Use case:** Laravel-aware coding assistance, artisan command support, package recommendations, and route/model insight during implementation.
- **Status:** Candidate
- **Notes:** Most relevant once Week 3 implementation starts. Prefer enabling after coding standards are finalized in `CONTRIBUTING.md`.

### 2. MySQL / Database
- **Category:** Database
- **Use case:** Schema inspection, migration linting, query review, seeding validation. Useful for Wakin’s backend work and QA data checks.
- **Status:** Candidate
- **Notes:** Should point only to local Docker or WAMP MySQL. Never point to production. Read-only mode preferred during review.

### 3. Docker / Compose
- **Category:** Infrastructure
- **Use case:** Compose validation, container health checks, runtime troubleshooting, image/volume inspection.
- **Status:** Candidate
- **Notes:** Useful for Fullstack Docker work and CI parity checks. Should not expose remote Docker sockets.

### 4. PHPUnit / Pest Testing
- **Category:** Testing
- **Use case:** Run tests, collect coverage, inspect failures, and suggest fixes for Livewire/feature tests.
- **Status:** Candidate
- **Notes:** Already supported by `vendor/bin/phpunit` and `phpunit.xml`. MCP wrapper is optional convenience.

### 5. Google Docs / Bible Keeper
- **Category:** Docs / Alignment
- **Use case:** Read and update the Project Bible, align Meetings tab content, detect conflicts, and draft Open Floor items.
- **Status:** Candidate
- **Notes:** Must use existing Google token path. Writes require explicit approval; never auto-push to Bible Center without review.

### 6. GitHub / Git workflow
- **Category:** VCS / CI
- **Use case:** PR review, branch policy checks, CI failure diagnosis, commit discipline enforcement.
- **Status:** Candidate
- **Notes:** Already enforced partly by `.github/workflows/test.yml`. MCP layer is optional acceleration.

### 7. Browser automation
- **Category:** QA / UI
- **Use case:** Livewire UI verification, upload form QA, role-gate page checks, responsive QA snapshots.
- **Status:** Candidate
- **Notes:** Prefer headless/offline-capable flows. Avoid external dependencies; keep checks deterministic.

### 8. File / Storage audit
- **Category:** Storage / Security
- **Use case:** Upload validation, private/public disk boundary checks, MIME/size enforcement, orphaned file detection.
- **Status:** Candidate
- **Notes:** Read-only scans preferred during QA. Write/delete actions must be manual and logged.

### 9. CI/CD pipeline
- **Category:** Automation
- **Use case:** GitHub Actions tuning, artifact management, failure diagnosis, deploy gate checks.
- **Status:** Candidate
- **Notes:** First-pass CI is already in `.github/workflows/test.yml`. MCP support is optional optimization.

---

## Activation checklist for any candidate
1. Add entry to this registry with purpose and review date.
2. Confirm it does not violate local-only or offline constraints.
3. Run a read-only dry run before any write-capable mode.
4. Add usage note to `CONTRIBUTING.md` when enabled.
