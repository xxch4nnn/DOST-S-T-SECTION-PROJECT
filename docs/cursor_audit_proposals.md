# Cursor Audit Proposals — DOSTorage V1

**Date:** 2026-07-22  
**Scope:** Hermes, Skills, MCP, GitHub, local Docker workflows  
**Primary context:** `docs/cursor_audit_brief.md`  
**Boundary:** No edits applied to `planning/`, `CONTRIBUTING.md`, or `AGENTS.md`. No secrets exposed.  
**Status:** Proposals only — review before applying.

## Current-state summary

| Area | How it works today | Gaps |
|------|--------------------|------|
| **Hermes** | Orchestrates outside the repo (delegation, Bible sync, GitHub governance). Cursor reaches Hermes via global MCP (`C:\Users\Asus\.cursor\mcp.json` → `user-Hermes`, verified). | Handoff loop is mostly manual paste; bridge docs live in gitignored `CURSOR_INTEGRATION_BRIEF.md`. Legacy `settings.json` Hermes MCP entry unused. |
| **Skills** | Hermes procedural templates under `~/.hermes/skills/` (e.g. Bible Keeper). Not loaded by Cursor. | No in-repo map of which skills produce Cursor briefs; Cursor cannot discover skill contracts. |
| **MCP** | Registry in `planning/MCP_SERVERS.md` — all **candidates**, none auto-enabled. Hermes MCP is local/global only. Browser MCP available in Cursor IDE. | Week-3 Laravel Boost / local MySQL / Docker MCP not activated; registry activation checklist not mirrored in Cursor config. |
| **GitHub** | CI (test + pint), PR/issue templates, CODEOWNERS, Dependabot, branch protection (1 approval). | First-push exclude `planning/archive/` still tracked (20 files). `docs/cursor_audit_proposals.md` is gitignored, blocking Hermes reconcile. |
| **Docker** | `Dockerfile` (php:8.3-fpm + extensions), `compose.yaml` (app + mysql:8.4), `/health` route. README says `docker compose up -d`. | Image has no CMD/entrypoint; compose does not start Artisan/HTTP; app service mounts MySQL volume; `.env.example` defaults to SQLite while compose expects MySQL; `scripts/start.sh` is host-PHP path, not Compose. |
| **Scripts** | Local `scripts/start.sh`, `scripts/test.sh`, `bible_keeper.bat` exist. Checklist requires them. | Entire `/scripts` is gitignored and **untracked** — clean clone cannot run checklist validation scripts. |

---

## Ranked proposals

Impact: H / M / L · Effort: S / M / L · Score = impact weight (H=3,M=2,L=1) vs effort (S=1,M=2,L=3) — listed best ROI first.

### 1. Untrack `planning/archive/` and ignore it — Impact **H** · Effort **S**

**Why:** Explicit first-push exclude; 20 archived files still tracked (`git ls-files planning/archive`). Blocks “1st official push” cleanliness.

**Proposed change (apply only when asked — touches `planning/` tracking + `.gitignore`):**
```bash
git rm -r --cached planning/archive
# add to .gitignore:
/planning/archive/
```
Keep files on disk if needed locally; stop shipping stale reports/gantt drafts.

**Acceptance:** `git ls-files planning/archive` empty; `.gitignore` contains `/planning/archive/`.

---

### 2. Stop gitignoring the audit proposals deliverable — Impact **H** · Effort **S**

**Why:** Hermes handoff requires committing/reconciling `docs/cursor_audit_proposals.md`, but `.gitignore` has `/docs/cursor_audit_proposals.md`. That breaks the intended loop.

**Proposed change:** Remove that ignore line (and keep `docs/cursor_audit_brief.md` tracked). Optionally add `docs/cursor_audit_proposals.md` to PR checklist as “review before merge.”

**Acceptance:** File is trackable; Hermes can read it from Git after push.

---

### 3. Resolve scripts vs first-push exclude contradiction — Impact **H** · Effort **S–M**

**Why:** `.gitignore` has `/scripts` and no scripts are tracked, yet `docs/checklist.md` First PR requires `scripts/start.sh` and `scripts/test.sh`. Clean clone fails the checklist.

**Proposed change (pick one policy, document in README — do not expand planning governance without ask):**
- **Preferred:** Track only approved validation scripts (`start.sh`, `test.sh`) via negation rules:
  ```gitignore
  /scripts/*
  !/scripts/start.sh
  !/scripts/test.sh
  ```
  Keep `bible_keeper.bat` and other Hermes helpers local-only.
- **Alt:** Keep `/scripts` fully excluded; rewrite checklist First PR items to use `composer` / `php artisan test` / `docker compose` only (matches CI).

**Acceptance:** Fresh clone can satisfy First PR checklist without private files.

---

### 4. Make Docker path actually bootable — Impact **H** · Effort **M**

**Why:** README claims offline Docker; current image/compose cannot serve the app as written.

**Findings:**
- `Dockerfile` installs PHP extensions + Composer but has no `CMD`/`ENTRYPOINT`.
- `compose.yaml` `app` service mounts `sail-mysql` volume (DB data on app container — wrong).
- No process starts `php artisan serve` (or nginx/php-fpm + web server).
- `.env.example` defaults `DB_CONNECTION=sqlite` while Compose injects MySQL host vars.

**Proposed change:**
1. Add a clear runtime command (e.g. `php artisan serve --host=0.0.0.0 --port=8000`) or a small entrypoint script.
2. Remove MySQL volume from `app`; keep it only on `mysql`.
3. Align `.env.example` commented MySQL block with Compose defaults (`DB_HOST=mysql`, database/user names) while allowing SQLite for non-Docker local.
4. Document two supported paths in README: **Docker Compose** vs **host PHP + `scripts/start.sh`**.

**Acceptance:** `docker compose up --build` → `/health` returns `{"status":"ok"}`; MySQL healthcheck green.

---

### 5. Align local validation with CI — Impact **M** · Effort **S**

**Why:** CI runs migrate + `php artisan test` + rollback + pint. Local `scripts/test.sh` is close; `scripts/start.sh` seeds then tests but never exercises Compose. Checklist also requires `docker compose config --quiet` and `/health`.

**Proposed change:**
- Add a thin `scripts/doctor.sh` (or document commands) that runs: `docker compose config --quiet`, optional compose up, curl `/health`, `vendor/bin/pint --test`, `scripts/test.sh`.
- Or extend First PR checklist to mark Docker items as “when using Compose” vs host-PHP.

**Acceptance:** One documented command set matches CI + checklist without secret leakage.

---

### 6. Track shareable Cursor guardrails; keep machine paths local — Impact **M** · Effort **S**

**Why:** `CURSOR_INTEGRATION_BRIEF.md` wants `.cursorrules` + `CURSOR.md`, but both are gitignored. Local `.cursorrules` already encodes stack + CONTRIBUTING alignment + Livewire naming + no-secrets — useful team-wide. Personal paths belong in `AGENTS.md` (do not edit unless asked).

**Proposed change:**
- Allow tracking `.cursorrules` (remove from `.gitignore`) with **repo-safe** rules only (no absolute Windows paths, no tokens).
- Add tracked `CURSOR.md` with: branch naming from CONTRIBUTING, “don’t edit planning/CONTRIBUTING/AGENTS unless asked”, test/pint commands, Hermes-owns-orchestration note.
- Keep `CURSOR_INTEGRATION_BRIEF.md` local/gitignored **or** fold non-secret MCP launch notes into `docs/cursor_audit_brief.md`.

**Acceptance:** New contributor opens repo in Cursor and gets naming/guardrails without copying Chan’s machine paths.

---

### 7. PR template + agent branch hygiene — Impact **M** · Effort **S**

**Why:** CONTRIBUTING requires `feat/<task-id>-…`, task ID in PR title, squash-only. PR template has Task IDs but not branch-name reminder.

**Proposed change:** Extend `.github/PULL_REQUEST_TEMPLATE.md` with:
- Branch name check (`feat/` or `fix/` + task id)
- Link to `docs/checklist.md` First PR
- “No push to `master`; no secrets; no `planning/` drive-bys”

Optional: Cursor rule “create feature branch before commits.”

**Acceptance:** PRs consistently include task ID + branch convention.

---

### 8. Hermes ↔ Cursor handoff contract (docs only) — Impact **M** · Effort **S**

**Why:** Skills inform Hermes; Cursor executes. Bridge works (`user-Hermes` ready), but operators still paste prompts manually — correct for reliability, under-documented in tracked docs.

**Proposed change:** Add `docs/cursor_handoff.md` (tracked) describing:
1. Hermes writes brief under `docs/` or issue/PR body.
2. Human pastes audit/implement prompt into Cursor Agents.
3. Cursor writes proposals/patches; Hermes reconciles after review.
4. Forbidden unsupervised paths: `planning/` governance, `CONTRIBUTING.md`, `AGENTS.md`, client-facing artifacts.
5. Pointer: global Hermes MCP is optional accelerator, not required for coding.

**Acceptance:** New operator can run the loop without Discord archaeology.

---

### 9. Enable MCP candidates deliberately (not now) — Impact **M** · Effort **M** (later)

**Why:** Registry correctly keeps MCP optional. Premature enablement risks production DB / Bible write accidents.

**Proposed phased enable (standup approval each):**
| Phase | Server | Mode |
|-------|--------|------|
| Week 3 coding | Laravel Boost | Local IDE |
| Backend/QA | MySQL MCP | Read-only → Docker/WAMP only |
| Fullstack | Docker/Compose MCP | Local socket only |
| UI QA | Browser (already in Cursor) | Headless/deterministic |
| Defer | Google Docs / Bible Keeper MCP | Write gated; prefer existing Keeper skill |

**Acceptance:** Each enable follows `planning/MCP_SERVERS.md` activation checklist; no production endpoints.

---

### 10. Compose defaults hygiene — Impact **L** · Effort **S**

**Why:** `MYSQL_ALLOW_EMPTY_PASSWORD: 1` alongside set passwords is confusing; default password `secret` is fine for local but should stay `.env`-driven only (already is).

**Proposed change:** Drop `MYSQL_ALLOW_EMPTY_PASSWORD` unless intentionally supporting empty local root; document that Compose defaults are **local-only**.

**Acceptance:** `docker compose config` validates; no empty-password surprise.

---

### 11. Livewire naming snippet / file template — Impact **L** · Effort **S**

**Why:** CONTRIBUTING warns silent breaks on Livewire name mismatches.

**Proposed change:** Add a short example under `docs/` or `CURSOR.md` (`php artisan make:livewire …` → class + blade path). No new scaffolding code until Week 3 features start.

**Acceptance:** Agents stop inventing mismatched component names.

---

### 12. Skills inventory pointer (read-only) — Impact **L** · Effort **S**

**Why:** Bible Keeper and related skills live outside Git; Cursor audits cannot see them.

**Proposed change:** In `docs/cursor_handoff.md`, list skill *names* and owners only (no token paths, no script dumps). Point Hermes operators to `~/.hermes/skills/…` without committing skill bodies.

**Acceptance:** Cursor knows Skills are Hermes-side; does not try to “fix” skills in-repo.

---

## Explicitly not proposed

- Editing `CONTRIBUTING.md`, `AGENTS.md`, or rewriting `planning/` content in this pass.
- Auto-enabling any MCP server.
- Committing `.env`, Google tokens, or Hermes local credentials.
- Using computer-use / UI automation as the primary way to invoke Cursor agents (manual paste remains the reliable path).
- Removing legacy Roaming `settings.json` Hermes MCP entry (harmless; optional cleanup later).

---

## Suggested apply order

1. Proposal **1** (`planning/archive/` untrack + ignore) — first-push compliance.  
2. Proposal **2** (track proposals file) — close Hermes↔Cursor audit loop.  
3. Proposal **3** (scripts policy) — make checklist honest.  
4. Proposal **4** (Docker boot) — match README claim.  
5. Then **5–8** as onboarding polish; **9** when implementation week starts.

## Review ask

Confirm which proposals Hermes/team want applied in the next PR. Highest urgency for official push cleanliness: **1**, then **2** + **3**.
