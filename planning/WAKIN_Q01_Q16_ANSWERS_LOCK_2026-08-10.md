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
| **Q02** | B | Prefer Wakin’s side on conflicts, then rewrite master callers | **BLOCKED** — needs `CHAN_ACK Q02: B` (or force to A) |
| **Q03** | A | Keep mother `scholarships` / `scholarship_types` | **Active** — strip `ScholarshipProgram*` from ports |
| **Q04** | A | Keep `spas_no` | **Active** |
| **Q05** | B | UUID thin `documents` + bytes on versions | **BLOCKED** — needs `CHAN_ACK Q05: B` **and** RFC PR in `docs/db/` before any schema merge |
| **Q06** | A | Keep `audit_logs.record_type` / `record_id` | **Active** — no `loggable_*` rename |
| **Q07** | C | Folders code may exist but **unmigrated / no routes** | **Active** — do not run folders migration in default seed |
| **Q08** | C | Port **ScholarObserver only** now; Document later | **Active** — PR `feat/be-58-scholar-observer` → #56 |
| **Q09** | A | Port search after documents shape + primary-key ACK | **BLOCKED by Q05=B** — questionnaire required Q05=A for this path; need re-answer or Chan hybrid |
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
- **Needs Chan:** either `CHAN_ACK Q02: B` with explicit file exceptions, or override to **Q02=A** (recommended).

### C3 — Q05=B vs Q09=A and Q10=A
- Search (Q09) and practical viewer port (Q10) assume mother document APIs unless UUID RFC is merged first.  
- **Needs Chan:**  
  - `CHAN_ACK Q05: B` + RFC track, **or**  
  - Ask Wakin to switch to **Q05=A** (or **C** hybrid) so viewer/search can ship now.

### C4 — Still conflicting after “handled conflicts”
- Tip `9097ccf` claims conflict handling; GitHub still reports **DIRTY** (at least `CHANGELOG.md`).  
- Agents must not assume conflict-free until `mergeable=MERGEABLE`.

### C5 — PIN_SHA freshness
- Wakin warns SHA outdated tonight. Before each port: `git fetch` and confirm SHA with him or use `origin/db-integration` tip + comment update.

---

## Chan ACK checklist (required before automation)

```text
CHAN_ACK Q02: <A|B>     # recommend A
CHAN_ACK Q05: <A|B|C>   # recommend A or C; B only with RFC
CHAN_ACK Q07: C         # confirm folders stay unmigrated (optional confirm)
```

Recommended Chan pack for fastest land of viewer:

```text
CHAN_ACK Q02: A
CHAN_ACK Q05: A
CHAN_ACK Q07: C
```

If Chan wants Wakin’s UUID redesign:

```text
CHAN_ACK Q02: A
CHAN_ACK Q05: B
CHAN_ACK Q07: C
```
Then agents open `docs/db/DOCUMENTS_UUID_RFC.md` PR **before** any code port that depends on UUID docs; Q09/Q10 wait on that RFC merge (or port viewer against mother interim).

---

## Executable slice order (after Chan ACK)

Assuming recommended Chan pack (**Q02=A, Q05=A**):

| Order | Branch | Scope | Source |
|------:|--------|-------|--------|
| 1 | `feat/be-58-viewer` | Viewer/print/download/zoom on mother Document APIs | Port from `PIN_SHA` / tip; Q10=A |
| 2 | `feat/be-58-scholar-observer` | ScholarObserver → `record_*` | Q08=C |
| 3 | `feat/be-58-search` | Dashboard search | Q09=A after Q9 primary-key ACK |
| — | — | Folders | Q07=C — no migration in seed |
| — | — | #68 | Convert to **Draft** or close as superseded |

If Chan ACKs **Q05=B** instead: insert RFC PR as slice 0; delay 1–3 until schema RFC merged **or** still do slice 1 against mother temporarily (viewer only).

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
| Automation ready | ❌ blocked on Chan ACK Q02/Q05 (+ clarify C1 with Wakin if needed) |
| #68 mergeable | ❌ |
| Next human action | Chan posts `CHAN_ACK` lines on #68 or #58 |
