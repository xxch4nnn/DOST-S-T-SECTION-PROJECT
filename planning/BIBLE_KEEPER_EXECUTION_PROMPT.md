# DOSTorage V1 — Bible Keeper Execution Prompt

**Date:** 2026-08-04  
**Bible status:** Migrated  
**New Bible location:** https://docs.google.com/document/d/1cmgb3y807KI5RSJABx93bC3PcI3rRFLpMBzCHg9x8xw/edit?tab=t.kasj3s9yik6e  
**General directory:** https://drive.google.com/drive/folders/1DO6QlLQsEicezwX6roCtacjgMYnjgLUG  
**Local repo:** `C:\Users\Asus\Documents\Personal\Programs\DOSTorage`  
**Planning folder:** `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning`

---

## Objective

Execute Bible Keeper with enhanced scope against the migrated Bible. Because Google Docs/Drive content is not directly extractable in this environment, treat the local planning folder as the **canonical Bible proxy** for this run, and produce the full handoff pack plus changelog updates.

---

## Steps

### 1. Bible-vs-Implementation Comparison
- Scan `planning/dostorage-v1-project-checklist.md` and `planning/dostorage-v1-backend-checklist.md`.
- Diff against `app/Livewire/**`, `app/Http/Controllers/**`, `database/migrations/**`, and `config/permission.php`.
- Produce coverage matrix with status per task: ✅ / 🟡 / ❌ / ⚠️.
- Flag security gaps: missing middleware, unguarded routes, soft-delete mismatches, unauthenticated download endpoints.

**Output:** `planning/HANDOFF_COVERAGE_MATRIX.md`

---

### 2. Artifact Lifecycle Management
- Confirm `planning/TASKS_DETECTED_payload.md` is retired from all active workflows.
- Ensure no standup, reporting, or Bible sync flow references it.
- If found, replace references with checklist + CSV task lists.

**Output:** Update `planning/HANDOFF_GUIDE.md` and `planning/HANDOFF_GITHUB_ISSUES.md` if needed.

---

### 3. Handoff Artifact Generation
Generate or refresh:
- `planning/HANDOFF_GUIDE.md` — Chan-scoped actions only.
- `planning/HANDOFF_GITHUB_ISSUES.md` — Non-Chan work split by owner: Wakin, Rui, Miguel.
- `planning/HANDOFF_COVERAGE_MATRIX.md` — Detailed coverage matrix.

**Output:** Three files saved to `planning/`.

---

### 4. Changelog Updates
- Append to `planning/AGENTIC_CHANGELOG.md` with Date, Actor, Repo+branch, Action, Commit/PR, Summary, Linked artifacts.
- Update `CHANGELOG.md` under `[Unreleased]` for any retired artifacts, new handoff pack, or behavior gaps identified.

**Output:** `planning/AGENTIC_CHANGELOG.md` and `CHANGELOG.md` updated.

---

### 5. GitHub Issue Registry
- Convert audit register into ready-to-paste GitHub Issues.
- Include labels, assignees, priorities, acceptance criteria, and checklists.
- Ensure P0 security items are separated from P1/P2 feature gaps.

**Output:** `planning/HANDOFF_GITHUB_ISSUES.md`

---

### 6. Bible Center Sync
Because direct Google Docs access is unavailable in this environment, prepare a **sync-ready summary** for manual paste into the new Bible document:
- Summary of handoff artifacts
- List of deprecated artifacts
- Next actions by owner
- Conflict tags to apply: `[CONFLICT]`, `[DECISION]`, `[OPEN]`, `[CONFIRMED]`

**Output:** `planning/BIBLE_CENTER_SYNC_SUMMARY.md`

---

## Constraints

- Do not overwrite client-facing Bible sections without PM approval.
- Do not generate or restore `TASKS_DETECTED_payload.md`.
- Keep all outputs inside `planning/`.
- Maintain Chan-scoped vs non-Chan scoping: Chan actions in `HANDOFF_GUIDE.md`; everything else in `HANDOFF_GITHUB_ISSUES.md`.

---

## Expected Deliverables

| File | Purpose |
|------|---------|
| `planning/HANDOFF_COVERAGE_MATRIX.md` | Bible checklist vs implementation coverage |
| `planning/HANDOFF_GUIDE.md` | Chan-scoped immediate actions |
| `planning/HANDOFF_GITHUB_ISSUES.md` | GitHub Issues for Wakin, Rui, Miguel |
| `planning/BIBLE_CENTER_SYNC_SUMMARY.md` | Ready-to-paste Bible Center summary |
| `CHANGELOG.md` | Product changelog updates under `[Unreleased]` |
| `planning/AGENTIC_CHANGELOG.md` | Agent/ops trail entries |

---

## Execution

Run this prompt now against the local repo and planning folder. After completion, return:
1. List of files created/updated
2. Any blockers or missing evidence
3. Suggested next sync time
