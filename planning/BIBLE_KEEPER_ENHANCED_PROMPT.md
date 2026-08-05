# DOSTorage V1 — Bible Keeper Enhanced Scope & Handoff Prompt

**Date:** 2026-08-04  
**Trigger:** Project Bible migrated to new Google Drive/Docs location  
**New Bible location:** https://docs.google.com/document/d/1cmgb3y807KI5RSJABx93bC3PcI3rRFLpMBzCHg9x8xxw/edit?tab=t.kasj3s9yik6e  
**General directory:** https://drive.google.com/drive/folders/1DO6QlLQsEicezwX6roCtacjgMYnjgLUG

---

## Enhanced Bible Keeper Scope

Bible Keeper is no longer a task-payload generator only. Its scope now includes:

1. **Bible vs Implementation Comparison**
   - Scan `planning/dostorage-v1-project-checklist.md` and `planning/dostorage-v1-backend-checklist.md` against implemented code.
   - Produce coverage matrix: ✅ implemented / 🟡 partial / ❌ missing.
   - Flag security gaps: missing middleware, unguarded routes, soft-delete mismatches.

2. **Artifact Lifecycle Management**
   - Mark deprecated artifacts as retired: `planning/TASKS_DETECTED_payload.md` is no longer maintained.
   - Update `planning/AGENTIC_CHANGELOG.md` for every investigation, audit, or handoff session.
   - Update root `CHANGELOG.md` under `[Unreleased]` for any behavior/schema/CI change.

3. **Handoff Artifact Generation**
   - Generate `planning/HANDOFF_GUIDE.md` (Chan-scoped actions only).
   - Generate `planning/HANDOFF_GITHUB_ISSUES.md` (non-Chan work split by owner: Wakin, Rui, Miguel).
   - Generate `planning/HANDOFF_COVERAGE_MATRIX.md` (detailed CSV-to-controller diff).

4. **GitHub Issue Registry**
   - Convert audit register into ready-to-paste GitHub Issues with labels, assignees, priorities.
   - Track P0 security items separately from P1/P2 feature gaps.

5. **Bible Center Sync**
   - Write summaries to the new Bible document: https://docs.google.com/document/d/1cmgb3y807KI5RSJABx93bC3PcI3rRFLpMBzCHg9x8xw/edit?tab=t.kasj3s9yik6e
   - Use conflict tags: `[CONFLICT]`, `[DECISION]`, `[OPEN]`, `[CONFIRMED]`.
   - Do not overwrite client-facing sections without PM approval.

---

## Files to Hand Off

### Primary Handoff Pack (audit-generated)
| File | Purpose | Owner |
|------|---------|-------|
| `planning/HANDOFF_GUIDE.md` | Chan-scoped immediate actions | Chan |
| `planning/HANDOFF_GITHUB_ISSUES.md` | GitHub Issues for Wakin, Rui, Miguel | Chan |
| `planning/HANDOFF_COVERAGE_MATRIX.md` | Bible checklist vs implementation coverage | Chan |

### Supporting Planning Artifacts (source of truth)
| File | Purpose |
|------|---------|
| `planning/dostorage-v1-project-checklist.md` | Master checklist with all epics/tasks |
| `planning/dostorage-v1-backend-checklist.md` | Backend contract: schemas, indexes, migration order |
| `planning/SPATIE_ROLES_BASELINE.md` | Verified roles/permissions matrix |
| `planning/TEAM_WORKFLOW.md` | Team rituals, burn-down, Bible Keeper schedule |
| `planning/long_range_planning.md` | 648h burn plan, phase gates |
| `planning/team_backend.csv` | Backend task breakdown |
| `planning/team_frontend.csv` | Frontend task breakdown |
| `planning/team_pm.csv` | PM/UI-UX task breakdown |
| `planning/team_qa.csv` | QA task breakdown |

### Changelog Artifacts (institutionalized)
| File | Purpose |
|------|---------|
| `CONTRIBUTING.md` | Changelog policy: product + agent/ops + stitch logs |
| `CHANGELOG.md` | Product behavior changelog under `[Unreleased]` |
| `planning/AGENTIC_CHANGELOG.md` | Agent/ops trail for investigations and audits |
| `planning/STITCH_EXECUTION_LOG.md` | Backend-to-mother stitch execution log |

### Deprecated / Archived
| File | Status |
|------|--------|
| `planning/TASKS_DETECTED_payload.md` | Retired. Do not use for current status. Keep only as archived reference. |

---

## Handoff Checklist

- [ ] Bible Keeper updated with new Google Docs URL and enhanced scope
- [ ] `HANDOFF_GUIDE.md` published to repo and reviewed by Chan
- [ ] `HANDOFF_GITHUB_ISSUES.md` opened as actual GitHub Issues
- [ ] `HANDOFF_COVERAGE_MATRIX.md` attached to project wiki or planning folder
- [ ] `AGENTIC_CHANGELOG.md` updated with handoff session entries
- [ ] `CHANGELOG.md` updated under `[Unreleased]` for retired payload and new artifacts
- [ ] Team notified: deprecated payload is no longer authoritative
- [ ] Next sync scheduled: 2026-08-06 10:00 AM

---

## Prompt for Bible Keeper Execution

```
You are Bible Keeper for DOSTorage V1.

Context:
- Project Bible has been migrated to: https://docs.google.com/document/d/1cmgb3y807KI5RSJABx93bC3PcI3rRFLpMBzCHg9x8xw/edit?tab=t.kasj3s9yik6e
- General directory: https://drive.google.com/drive/folders/1DO6QlLQsEicezwX6roCtacjgMYnjgLUG
- Local repo: C:\Users\Asus\Documents\Personal\Programs\DOSTorage
- Planning folder: C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning

Your enhanced scope:
1. Compare implementation against Bible checklist and produce coverage matrix.
2. Flag security gaps: missing permission gates, unguarded routes, soft-delete mismatches.
3. Generate handoff artifacts:
   - HANDOFF_GUIDE.md (Chan-scoped actions only)
   - HANDOFF_GITHUB_ISSUES.md (non-Chan work by owner)
   - HANDOFF_COVERAGE_MATRIX.md (detailed coverage)
4. Update changelogs:
   - Append to planning/AGENTIC_CHANGELOG.md
   - Update CHANGELOG.md under [Unreleased]
5. Sync summaries to Bible Center using conflict tags: [CONFLICT], [DECISION], [OPEN], [CONFIRMED].
6. Do NOT overwrite client-facing sections without PM approval.

Deprecated artifacts:
- TASKS_DETECTED_payload.md is retired. Do not generate or restore it.

Output:
- Save all artifacts to C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning\
- Return a summary of generated files and any blockers.
```

---

*This prompt should be used to reinitialize Bible Keeper with the new scope and migrated Bible location.*
