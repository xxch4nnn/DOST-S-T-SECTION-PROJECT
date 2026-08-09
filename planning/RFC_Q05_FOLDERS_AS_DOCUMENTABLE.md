# RFC Q05 — Folders as a documentable target

**Status:** ✅ Decided — **Park for post-V1** (Option B + Q07=C)  
**Owner:** Wakin (`@WakenMac`) — proposed in PR #68 (`Db integration`)  
**Decision owner:** Chan (`@xxch4nnn`)  
**Decided:** 2026-08-10 03:01 +08:00  
**Locked V1 plan date:** 2026-07-29

---

## Decision

**Park folders for post-V1.** Folders code may exist on `db-integration` but:

- ❌ No `folders` migration runs in default seed
- ❌ No routes or UI for folders in V1
- ✅ `db-integration` branch stays as a lab reference for the concept

Chan followed Wakin's Q07=C answer: *"Folders code may exist but unmigrated / no routes."*

---

## Problem (original)

Administering DOST-SEI documents currently has only loose organization:

- `scholars` → documents owned by a scholar
- `administrative_records` → flat records by `record_type` + `year`

There is no folder/office/category hierarchy for administrative files. Staff want to browse docs by office or topic, not just by record type.

PR #68 introduced a `folders` table and made it a 3rd polymorphic `documentable` target.

---

## Why Parked

| Risk | Detail |
|------|--------|
| Schema drift | 3rd polymorphic `documentable` expands V1 scope |
| Timeline | Folders UI: tree view, drag-drop, breadcrumbs, filters ≈ 2–3 weeks |
| Conflicts with master | PR #68 is `CONFLICTING`; renames `scholarships` → `ScholarshipProgram*` |
| Breaks PR #65 | `documents` UUID-only shape disrupts upload/download |
| Migration noise | Duplicate `2026_07_15_*` migrations + deleted seeders |

---

## Post-V1 Path

When folders are revisited:

1. Open a standalone feature issue (not bundled with other schema work)
2. Write an additive migration from clean `master` — do not re-use the `db-integration` migration as-is
3. Decide between full folder hierarchy vs. middle-path `office_id` FK (see below)
4. DOST staff confirmation of need before implementation

### Middle path — `office_id` on `administrative_records` (available if V1 needs light categorization)

- Add `office_id` FK to `administrative_records`
- Office dropdown on create/edit
- Filter by Office on admin records index
- **Effort:** ~3–5 days
- **Coverage:** ~80% of current folder need
- **Risk:** Low — single column, no polymorphic expansion

---

## Decision Record

```
CHAN_ACK Q07: C     — folders stay dormant (no migration, no routes)
Decided: 2026-08-10 03:01 +08:00
Rationale: Follow Wakin's answers; focus V1 on core document management.
           Folders are a post-V1 feature to revisit when DOST staff confirm need.
```

