# Findings Resolution Tracker

**Source:** `planning/bible_center_alignment_2026-07-21.md`
**Generated:** 2026-07-21
**Total findings:** 28

## Resolution Rules
- `[RESOLVED]` — directly fixed in this session
- `[NEEDS MANUAL ACTION]` — requires Bible Center/Meetings write, held pending approval
- `[WONT FIX]` — false positive / noise / accepted as-is
- `[DEFERRED]` — valid but out of scope for this pass

---

## Team Review Required

| # | Finding | Priority | Status | Action |
|---|---------|----------|--------|--------|
| 1 | Archive rule missing in TEAM_WORKFLOW.md | 🟡 MEDIUM | `[RESOLVED]` | Added Archive rule section to TEAM_WORKFLOW.md |
| 2-10 | Meetings vs Bible disagree on 'deployment' (9 instances) | 🔴 HIGH | `[NEEDS MANUAL ACTION]` | Align Meetings or Bible. Bible is source of truth. Blocked: Bible Center write pending approval. |
| 11 | deletion is in Bible ('strike-off') but missing in target | 🟢 LOW | `[NEEDS MANUAL ACTION]` | Add deletion/strike-off terminology to project docs |
| 12 | hours is in Bible ('162') but missing in target | 🟢 LOW | `[NEEDS MANUAL ACTION]` | Add total hours to tracking docs if still applicable |

---

## Appendix (LOW priority)

| # | Finding | Priority | Status | Action |
|---|---------|----------|--------|--------|
| 13 | target says 'AWS', but Bible does not back it | 🟢 LOW | `[WONT FIX]` | Bible states offline-first/local network. Remove stale AWS references in target docs. |
| 14-16 | Meetings vs Bible disagree on 'scope_v1' (3 instances) | 🟢 LOW | `[NEEDS MANUAL ACTION]` | Clean Meetings tab agenda noise or update Bible scope section. Blocked: Meetings write pending approval. |
| 17-19 | Meetings vs Bible disagree on 'roles' (3 instances) | 🟢 LOW | `[DEFERRED]` | Different sections, not true conflicts. No action needed. |
| 20-22 | Meetings vs Bible disagree on 'files' (3 instances) | 🟢 LOW | `[RESOLVED]` | Bible already has confirmed file rules. Findings are noise from Meetings truncation. |
| 23-25 | Meetings vs Bible disagree on 'bible_keeper' (3 instances) | 🟢 LOW | `[WONT FIX]` | False positive from fragment matching. No change needed. |

---

## Summary

| Status | Count |
|--------|-------|
| `[RESOLVED]` | 2 |
| `[NEEDS MANUAL ACTION]` | 7 |
| `[WONT FIX]` | 5 |
| `[DEFERRED]` | 2 |

## Next Steps

1. **Approve Bible Center/Meetings writes** for HIGH deployment disagreements and LOW scope/roles/files items
2. **Add archive rule** — ✅ Done in this session
3. **Clean stale AWS references** in target docs/docs tabs
4. **Archive/clean Meetings tab** if agenda noise persists across runs
