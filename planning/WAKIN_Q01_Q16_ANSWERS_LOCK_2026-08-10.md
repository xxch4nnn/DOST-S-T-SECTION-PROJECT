# Wakin Q01–Q16 answers lock — PR #68 / #58

**Captured:** 2026-08-10  
**Source:** Wakin comment on [#68](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/68)  
**PIN_SHA:** `9097ccf` (he notes tip may move tonight for scholar CRUD)  
**PR tip status at capture:** still `CONFLICTING` vs `master`; CI red (test/lint/lint-css/synthetic-smoke)

---

## Normalized answer block

```text
Q01: A
Q02: B
Q03: A
Q04: A
Q05: B
Q06: A
Q07: C
Q08: C
Q09: A
Q10: A
Q11: A
Q12: A
Q13: A
Q14: A
Q15: A
Q16: A
PIN_SHA: 9097ccf
```

(Typos `Q010`–`Q016` normalized to `Q10`–`Q16`.)

---

## Per-question interpretation (agent-ready)

| Q | Letter | Meaning | Agent status |
|---|--------|---------|--------------|
| **Q01** | A | Thin cherry-picks from `master`; **do not merge #68 as-is** | **Active** — draft/close #68 after slice PRs exist |
| **Q02** | B | Prefer Wakin’s side on conflicts, then rewrite master callers | ✅ **CHAN_ACK Q02: B** — 2026-08-10 03:01 +08:00 |
| **Q03** | A | Keep mother `scholarships` / `scholarship_types` | **Active** — strip `ScholarshipProgram*` from ports |
| **Q04** | A | Keep `spas_no` | **Active** |
| **Q05** | B | UUID thin `documents` + bytes on versions | ✅ **CHAN_ACK Q05: B** — 2026-08-10 03:01 +08:00 · RFC: `docs/db/DOCUMENTS_UUID_RFC.md` |
| **Q06** | A | Keep `audit_logs.record_type` / `record_id` | **Active** — no `loggable_*` rename |
| **Q07** | C | Folders code may exist but **unmigrated / no routes** | **Active** — do not run folders migration in default seed |
| **Q08** | C | Port **ScholarObserver only** now; Document later | **Active** — PR `feat/be-58-scholar-observer` → #56 |
| **Q09** | A | Port search after documents shape + primary-key ACK | ✅ **Unblocked** — Q05=B ACK'd; search ports against UUID `document_versions` shape after viewer lands |
| **Q10** | A | Port viewer/print/download/zoom first | **Active** — first UI PR from `master` |
| **Q11** | A | Relative `database/sample_pdfs/**` only | **Active** |
| **Q12** | A | Fixture emails only | **Active** |
| **Q13** | A | Branches `feat/be-58-*` from `master` | **Active** (NOTES agree after merge problems fixed) |
| **Q14** | A | Full CI bar | **Active** |
| **Q15** | A | Agents write CHANGELOG | **Active** |
| **Q16** | A | Salvage from `PIN_SHA` | **Active** — re-fetch tip before each port; prefer latest `origin/db-integration` if Wakin updates PIN |

---

## Contradictions / clarifications needed

### C1 — Q01=A vs NOTES “fully integrate db-integration merge”
- **Q01=A** forbids squash-merging #68.  
- NOTES still describe merging `db-integration` into master first.  
- **Resolution for agents:** Honor **Q01=A**. Treat NOTES as “after thin PRs land, continue on `feat/be-58-*`.” Do **not** merge #68.

### C2 — Q02=B vs Q01=A / Q03=A
- Preferring “yours everywhere” fights mother scholarship naming (Q03=A) and thin-pick strategy.  
- **Resolved:** `CHAN_ACK Q02: B` (2026-08-10). Prefer Wakin on remaining conflicts **except** scholarship naming (Q03=A) and `audit_logs.record_*` (Q06=A); rewrite master callers to match.

### C3 — Q05=B vs Q09=A and Q10=A
- Search (Q09) and practical viewer port (Q10) assume mother document APIs unless UUID RFC is merged first.  
- **Resolved:** `CHAN_ACK Q05: B` + RFC `docs/db/DOCUMENTS_UUID_RFC.md`. Slice order: RFC → uuid-migration → viewer → scholar-observer → search.

### C4 — Still conflicting after “handled conflicts”
- Tip `9097ccf` claims conflict handling; GitHub still reports **DIRTY** (at least `CHANGELOG.md`).  
- Agents must not assume conflict-free until `mergeable=MERGEABLE`.

### C5 — PIN_SHA freshness
- Wakin warns SHA outdated tonight. Before each port: `git fetch` and confirm SHA with him or use `origin/db-integration` tip + comment update.

---

## Chan ACK checklist (required before automation)

```text
CHAN_ACK Q02: B     ✅ decided 2026-08-10 03:01 +08:00
CHAN_ACK Q05: B     ✅ decided 2026-08-10 03:01 +08:00 — RFC written: docs/db/DOCUMENTS_UUID_RFC.md
CHAN_ACK Q07: C     ✅ decided 2026-08-10 03:01 +08:00 — folders stay dormant
```

Chan chose to follow Wakin's answers across the board. RFC created at `docs/db/DOCUMENTS_UUID_RFC.md`.

---

## Executable slice order (ACK’d — Q05=B)

| Order | Branch | Scope | Source |
|------:|--------|-------|--------|
| 0 | `docs/uuid-documents-rfc` | UUID documents RFC (docs-only) | This lock + `docs/db/DOCUMENTS_UUID_RFC.md` |
| 1 | `feat/be-58-uuid-migration` | Additive migrations + models/factories | RFC merged; answer RFC open Qs first |
| 2 | `feat/be-58-viewer` | Viewer/print/download/zoom via `currentVersion` | Migration merged; Q10=A |
| 3 | `feat/be-58-scholar-observer` | ScholarObserver → `record_*` | Migration merged; Q08=C |
| 4 | `feat/be-58-search` | Dashboard search on `document_versions` | Viewer merged; Q09=A |
| — | — | Folders | Q07=C — parked; no migration/routes |
| — | — | #68 | Convert to **Draft** or close as superseded |

---

## Hard stops (still true)

- Absolute Windows paths  
- Flat `files` / DomPDF  
- Merging #68 while `CONFLICTING` or CI red  
- Executing Q02=B or Q05=B without matching `CHAN_ACK`  
- Shipping folders migration under Q07=C  

---

## Status

| Item | State |
|------|--------|
| Answers received | ✅ |
| Chan ACKs posted | ✅ Q02=B, Q05=B, Q07=C — 2026-08-10 03:01 +08:00 |
| UUID RFC written | ✅ `docs/db/DOCUMENTS_UUID_RFC.md` |
| Folders RFC decided | ✅ Parked (Q07=C) — `planning/RFC_Q05_FOLDERS_AS_DOCUMENTABLE.md` |
| Automation ready | ✅ unblocked — slice PRs can begin |
| #68 mergeable | ❌ — convert to Draft; cherry-pick via thin slice PRs |
| Next action | Wakin opens slice PRs from `master` per execution order below |
