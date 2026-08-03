# DOSTorage V1 — Handoff Guide: Immediate Actions

**Date:** 2026-08-04  
**Audit basis:** Project Bible / planning docs vs `C:\Users\Asus\Documents\Personal\Programs\DOSTorage` implementation  
**Chan-scope actions:** Below. All non-Chan work has been split into tracked GitHub Issues in `planning/HANDOFF_GITHUB_ISSUES.md`.

---

## Chan Actions

| # | Action | Due |
|---|--------|-----|
| 1 | Retire `TASKS_DETECTED_payload.md` from all workflows; use checklist + CSV task lists only | ✅ Done 2026-08-04 |
| 2 | Publish `HANDOFF_GITHUB_ISSUES.md` to repo and assign owners | ✅ Issues filed (see GH links in AGENTIC_CHANGELOG) |
| 3 | Verify Bible Keeper runtime: Apps Script API enabled, `PROJECT_TASKS_TAB_ID=t.cm79ati3cwhz` set, live-write mode active | Day 5 |
| 4 | Verify CI/test execution evidence exists and is current | ✅ `planning/exports/phpunit_2026-08-04.txt` + CI on #35 |
| 5 | Add offline-queue migration/model scaffold if not completed by Wakin | Day 3 (tracked as GH issue → Wakin) |
| 6 | Review permission-gate patch before merge; confirm 403 behavior | Day 1 (tracked as GH issues → Wakin) |
| 7 | Schedule next sync: 2026-08-06 10:00 AM | ✅ Scheduled |

---

## Issue Tracker for Non-Chan Work

All P0–P2 work outside Chan scope is captured in `planning/HANDOFF_GITHUB_ISSUES.md`. Owners:

- **Wakin:** route-level Spatie enforcement, document download authorization, offline queue migration/model, audit-log user-deletion policy, default Super Admin seed state, QA test suite
- **Rui:** global search UX/backend action, strike-off/restore UI, component refinement
- **Miguel:** QA evidence artifacts, responsive/browser coverage, test plan creation

Do **not** track these items only in chat or local notes. They must be entered as GitHub Issues or they are not on the board.

---

## Decision Gate Checklist

| Gate | Criterion | Status |
|------|-----------|--------|
| Phase 1 complete | 40h learning per member done | ✅ Verified |
| Phase 2 start | All members through learning gate | ✅ Verified |
| P0 security | Route-level permission enforcement | ⏳ Pending patch |
| P0 docs | Deprecated payload retired from workflows | ✅ Done 2026-08-04 |
| P1 offline | Offline queue table exists | ⏳ Pending (GH issue → Wakin) |
| P1 QA | phpunit run + responsive evidence saved | 🟡 phpunit evidence saved; responsive still Miguel |

---

*This guide is Chan-scoped only. Cross-role work is in `planning/HANDOFF_GITHUB_ISSUES.md`.*
