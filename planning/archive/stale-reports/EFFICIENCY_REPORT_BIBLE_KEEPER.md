# Bible Keeper — Before/After Efficiency Report

## Context
- Project: DOST-SEI Davao Region Scholarship Records Management System (DOSTorage V1)
- Team: 4 members, 162-hour shared pool
- Tool: Project Bible Keeper — aligns Bible Center, Meetings tab, and `TEAM_WORKFLOW.md`
- Review date: 2026-07-17
- Change: Council-backed hybrid workflow replacing cron auto-write with dedupe-aware writing and read-only cron detection

---

## Before (Original Cron Auto-Write)

| Metric | Estimate | Source |
|--------|----------|--------|
| Cron runs per day | 2 (noon, 19:00) | Schedule |
| Auto-writes per run | 2 approved findings | July 16 run |
| Duplicate inserts per week | 14+ | No dedup, same findings repeat |
| Manual cleanup time per week | 30-60 min | Identifying stale duplicates in Open Floor |
| Risk of misaligned Bible | Medium-High | Detection conflated with commitment |
| Team habit tax | Zero for detection, growing for curation | Status quo |

### Workflow Diagram
```
Cron (12:00) -> scan -> auto-append -> Open Floor grows
Cron (19:00) -> scan -> auto-append -> Open Floor grows
Weekly cleanup -> manual review/archive
```

---

## After (Council-Backed Hybrid)

| Metric | Estimate | Source |
|--------|----------|--------|
| Cron runs per day | 2 (noon, 19:00) | Schedule unchanged |
| Auto-writes per run | 0 from cron | Read-only script |
| Potential duplicate inserts per week | 0 | Dedup before write |
| Manual review effort per day | <2 min | Read report at standup |
| Write effort per approved finding | <30 sec | Manual `--write` trigger |
| Risk of misaligned Bible | Low | Human gate on commitment |

### Workflow Diagram
```
Cron (12:00) -> scan -> report only -> alert if HIGH exists
Cron (19:00) -> scan -> report only -> alert if HIGH exists
Standup review -> manual `--write` -> write only NEW approved items
Resolved items -> [ARCHIVED] -> auto-skipped on future scans
```

---

## Efficiency Gains

### Quantitative
1. **Noise reduction**: Eliminates duplicate Open Floor writes entirely
2. **Curation debt**: O(1) vs O(n²) as Bible grows from ~22KB toward target ~8KB
3. **Write precision**: Only new approved HIGH/MEDIUM items enter the Bible
4. **False-positive suppression**: LOW Appendix items stay suppressed by default

### Qualitative
1. **Separation of concerns**: Detection ≠ commitment
2. **Auditable history**: Clean, curated decision register
3. **Scalability**: 4-person team can maintain without habit tax
4. **Compliance-ready**: Archive tags give closure evidence

---

## Tradeoffs
- **Gain**: Cleaner Bible, less weekly cleanup, lower drift risk
- **Cost**: One manual step per day if findings exist
- **Mitigation**: 30-second standup trigger; HIGH-only default keeps volume low

---

## Recommendation
Adopt the hybrid workflow permanently. The minor habit tax of a manual `--write` is outweighed by elimination of duplicate noise and preservation of the Bible as a canonical, curated source of truth.

---

## Appendix: What Changed

### Code Changes
- `bible_keeper.py`: Added `_extract_open_floor_bullets()` for dedup, `_archive_safe()` for skip logic
- Both cron jobs: switched from `script` with `--write` to read-only `python ... all`
- Removed: `bible_keeper_write.sh` deterministic write script
- Added: manual `--write` path with built-in dedup/archive defense

### Document Changes
- Bible Center Sections updated:
  - 2.1 Functional Requirements confirmed via FR-01…FR-10 table
  - 6.1 Search — `[DECISION] awaiting client resolution` with global/per-tab design
  - 10.2 ERD — additive extension wording consolidated, duplicate sentence removed
  - 6.13 duplicate “Roles” block removed
  - V1 scope duality resolved
  - AWS contradiction clarified
- Deliverable: `bible-patch-2026-07-17.md` for TEAM_WORKFLOW.md additions (delivered for CI/CD review)
