# Wakin handoff — remediate `db-integration` (#68) for agentic merge

**Date:** 2026-08-08  
**To:** Wakin (`@WakenMac`)  
**From:** Chan (`@xxch4nnn`) / agents  
**Trackers:** PR [#68](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/68) · Issue [#58](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/issues/58)  
**Related:** `planning/PR68_REVIEW_2026-08-08.md` · `planning/WAKIN_Q12_VS_IMPLEMENTATION_2026-08-06.md` · `planning/STITCH_IMPLEMENTATION_PLAN.md`  
**Mother tip (merge target):** `origin/master` (includes #65 upload/edit/notifications)

---

## Purpose

PR #68 **cannot** merge today (conflicts + red CI + schema divergence).  
This document collects **locked answers** from you so an agent can:

1. Resolve conflicts against current `master`  
2. Remap your work onto mother SoT (or apply an explicitly approved exception)  
3. Open thin, green PRs that finally land the useful parts  

**ACK** = you (or Chan for product items) explicitly confirm a choice.  
Agents must **not invent** answers. If a question is unanswered, stop and ask.

---

## How to answer (required format)

Reply in the PR #68 thread **or** paste into a new comment on issue #58 using **exactly** this shape (one block per question):

```text
Q01: A
Q02: B
Q03: A
...
NOTES: <optional free text; agents ignore unless it clarifies a choice>
PIN_SHA: <git SHA of lab or db-integration tip you want agents to treat as source>
```

Rules for agents after answers land:

| Rule | Behavior |
|------|----------|
| Letter choice | Execute the **Agent action** column for that letter only |
| Missing question | Abort merge automation; comment “blocked on Qxx” |
| `NOTES` that contradict a letter | Prefer letter; ask Chan if conflict is product-level |
| `OTHER` / freeform | Allowed only where the question says so; otherwise treat as unanswered |

---

## Current state (do not debate — facts)

| Fact | Detail |
|------|--------|
| Branch | `db-integration` @ `f046aa9` (update `PIN_SHA` if tip moved) |
| vs `master` | **CONFLICTING** |
| CI | test / lint / lint-css / synthetic-smoke **failing** |
| CODEOWNER | **CHANGES_REQUESTED** |
| Flat `files` table | Absent on tip (good) |
| Parallel naming | `ScholarshipProgram*` still present |
| Documents | UUID shell + bytes on versions (≠ mother file columns on `documents`) |
| Audit | `loggable_*` (≠ mother `record_*`) |
| Valuable salvage | Viewer render/zoom/print/download; observer ideas; search UI |

---

## Mother SoT (defaults if you pick “align to mother”)

These are already on `master` and are the **default merge target shape**:

| Area | Mother convention |
|------|-------------------|
| Scholarships | `scholarships` / `scholarship_types` · FKs `scholarship_id` / `scholarship_type_id` |
| Scholar key | `spas_no` (not `spas_number`) |
| Documents | bigint/id morph `documentable_*` + **file fields on `documents`** + history in `document_versions` (`stored_filename`, …) |
| Audit | `audit_logs.record_type` / `record_id` (+ optional payloads if additive) |
| File types | `file_groups` + `file_types.metadata_template`; no `file_types.year` |
| DomPDF | Never |
| Flat `files` | Never |
| Migrations | **Additive** only; do not replace `2026_07_15_*` history with parallel creates |

---

## Decision questions

### Block A — Merge strategy (answer first)

#### Q01 — How should agents land your work?

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | **Thin cherry-picks onto `master`** (recommended). Close or draft #68; open new PRs from `master`. | Do **not** merge #68. Create `feat/be-58-*` branches from `master`. Port only approved slices (see Q10–Q12). |
| **B** | **Remediate `db-integration` in place**, then merge #68 once green + conflict-free. | Reset/rebase onto `origin/master`, apply all “align to mother” answers as rewrites on the same branch, force-push only if Wakin confirms in NOTES. |
| **C** | **Abandon schema; keep UI only.** | Same as A, but skip any schema PR; UI-only PRs. |

#### Q02 — Conflict resolution authority when the same file exists on both sides

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Prefer **`master`** for schema/models/migrations/seeders; prefer **yours** only for viewer/print/download JS/Blade that does not rename tables. | On conflict: take master for `database/**`, `app/Models/**` (except new observer files mapped per Q08); manually re-apply viewer logic. |
| **B** | Prefer **yours** everywhere, then rewrite tests/master callers. | **Requires Chan NOTES ACK.** Agents must not choose B without Chan. |
| **C** | File-by-file list in NOTES. | Agents follow NOTES map only; abort if incomplete. |

---

### Block B — Naming / columns (schema)

#### Q03 — Scholarship naming

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | **Keep mother** `Scholarship` / `ScholarshipType` / `scholarship_id` / `scholarship_type_id`. | Delete `ScholarshipProgram*` models/migrations/seeders from merge path. Rewrite your Livewire/seeders to mother names. |
| **B** | Rename mother → `ScholarshipProgram*` globally. | **Requires Chan ACK in NOTES.** Separate migration PR + full test rewrite; not allowed inside UI PR. |

#### Q04 — Scholar SPAS column

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Keep mother **`spas_no`**. | Map all `spas_number` → `spas_no` in your code/seeders. |
| **B** | Rename mother to `spas_number`. | Chan ACK required; additive rename migration + update all callers. |

#### Q05 — `documents` row shape

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | **Mother shape:** file fields on `documents` (`file_type_id`, `original_filename`, `stored_filename`, `mime_type`, `file_size_kb`, `status`, `uploaded_by`, `metadata`) + `document_versions` for history (`stored_filename`, `version_number`, `replaced_by_user_id`, …). | Port viewer to mother `Document` / `DocumentVersion` APIs. Drop UUID-only document redesign from merge path. |
| **B** | **Your UUID documents** (thin document + bytes only on versions with `file_path` / `file_type_id` on version). | Chan ACK + written RFC in `docs/db/`; **blocked** from merge until RFC merged separately. Agents must not merge #68 under B until RFC PR exists. |
| **C** | Hybrid: mother columns **plus** UUID secondary key additive. | Chan ACK; additive migration only (`uuid` nullable unique); keep mother PK/file columns. |

#### Q06 — `audit_logs` columns

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Keep mother **`record_type` / `record_id`**. | Rewrite observers/notifications to mother columns. Do not rename to `loggable_*`. |
| **B** | Rename to `loggable_type` / `loggable_id`. | Chan ACK + migration PR updating all writers (login, #65 notifications, observers). |
| **C** | Keep `record_*` and **add** optional `before_payload` / `after_payload` JSON (additive). | Allowed without rename. Agents add nullable JSON columns if missing; map observers to fill them. |

#### Q07 — `folders` as `documentable` parent

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | **Defer V1** — no `folders` table in merge. | Strip folder migrations/models from merge path; documents morph to Scholar / AdministrativeRecord only. |
| **B** | Ship folders in V1. | **Chan product ACK required.** Separate PR after documents shape (Q05) locked; not bundled with viewer. |
| **C** | Keep code behind feature flag / unmigrated. | Do not run folders migration in default seed; no routes. |

---

### Block C — Features to port

#### Q08 — Observers

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Port `DocumentObserver` + `ScholarObserver` to mother audit columns (issue #56). | New PR from `master`; write `record_type`/`record_id` (+ payloads if Q06=C). No FileObserver. |
| **B** | Defer observers. | Do not port; leave #56 open. |
| **C** | Port Scholar only now; Document later. | One PR ScholarObserver only. |

#### Q09 — Smart search / dashboard search

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Port search UI **after** Q05=A and Q9 primary keys ACK. | Separate PR; query mother morph + `documents.metadata`. |
| **B** | Defer search. | Skip. |
| **C** | Port UI shell with mocked results only behind `local`. | Allowed only if no schema change; must not ship mocks to production env. |

#### Q10 — Document viewer / print / download / zoom

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Port to mother Document download + version latest file (priority). | PR from `master`: Blade/JS only; use `route('documents.download')` + authorize; no schema rename. |
| **B** | Defer viewer. | Skip. |
| **C** | Port viewer **and** change storage path scheme (`file_path` absolute/relative). | Specify path root in NOTES (`storage/app/documents` vs other). Default: mother `documents/{stored_filename}` on `local` disk. |

#### Q11 — Sample PDFs / seeders

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Use mother `database/sample_pdfs/**` + relative paths only. | Remove any absolute `C:\Users\...` paths; use `SamplePdfFixture` / relative seeder helpers. |
| **B** | You will supply a cleaned seeder in NOTES/PR. | Wait for your commit SHA in `PIN_SHA` / NOTES. |
| **C** | No sample PDF seeding in merge PRs. | Skip Document/File seeders beyond taxonomy. |

#### Q12 — Personal data in seeders

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Replace personal emails/names with fixtures (`test@example.com`, fake scholars). | Rewrite seeders accordingly before merge. |
| **B** | Keep your personal email as Super Admin seed. | **Rejected by default.** Requires Chan ACK (unlikely). |

---

### Block D — Process / agent execution

#### Q13 — Target branch naming for agent PRs

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | `feat/be-58-<slice>` from `master` (recommended). | Use `feat/be-58-viewer`, `feat/be-58-observers`, etc. |
| **B** | Continue on `db-integration` only (Q01=B). | All work on that branch. |
| **C** | You will create branches; agents only push fixes. | Agents wait for branch names in NOTES. |

#### Q14 — CI bar before requesting review

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | `php artisan test` + pint + lint-css + synthetic-smoke all green (required). | Do not ask for review until green. |
| **B** | Tests only. | Not accepted for merge to `master`. Treat as unanswered → default A. |

#### Q15 — CHANGELOG / task IDs

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Agents add CHANGELOG bullets (date-time + Chan/`@WakenMac`) and link **#58** (+ #56/#41 if relevant). | Mandatory on every behavior PR. |
| **B** | You will write CHANGELOG. | Agents still block merge if missing. |

#### Q16 — Lab pin

| Letter | Choice | Agent action |
|--------|--------|--------------|
| **A** | Treat `db-integration` @ `PIN_SHA` (you provide) as salvage source. | Cherry-pick/port from that SHA only. |
| **B** | Also pin external lab `WakenMac/DOST-RXI-OJT_SQL-Files` SHA in NOTES. | Prefer mother branch for UI; lab only if NOTES say so. |

---

### Block E — Chan / product ACK (Wakin proposes; Chan signs)

Agents **must not** execute B-options that say “Chan ACK” until Chan comments:

```text
CHAN_ACK Q03: <A|B>
CHAN_ACK Q05: <A|B|C>
CHAN_ACK Q06: <A|B|C>
CHAN_ACK Q07: <A|B|C>
CHAN_ACK Q12: <A|B>
```

**Recommended defaults for Chan** (edit if you disagree):

```text
CHAN_ACK Q03: A
CHAN_ACK Q05: A
CHAN_ACK Q06: C
CHAN_ACK Q07: A
CHAN_ACK Q12: A
```

---

## Suggested answer pack (fast path to merge)

If you want the **fastest** path to land viewer + observers without redesigning mother:

```text
Q01: A
Q02: A
Q03: A
Q04: A
Q05: A
Q06: C
Q07: A
Q08: A
Q09: B
Q10: A
Q11: A
Q12: A
Q13: A
Q14: A
Q15: A
Q16: A
PIN_SHA: f046aa9
NOTES: Port viewer/print/download first; then observers; defer search and folders.
```

---

## Agentic merge playbook (after answers)

Execute **only** after Q01–Q16 filled and required `CHAN_ACK` present.

### Phase 0 — Prep
1. `git fetch origin master db-integration`  
2. Verify `PIN_SHA` exists  
3. Create working branch per Q13  
4. Comment on #58: “Automation started with answers …”

### Phase 1 — Schema alignment (skip if Q01=C and no schema)
1. Apply Q03–Q07 transforms on a **schema** PR if any B/C needing migrations  
2. Prefer **no schema PR** when all of Q03–Q07 are A (or Q06=C additive only)  
3. `php artisan migrate:fresh --seed` + `php artisan test` green

### Phase 2 — Viewer PR (if Q10=A)
1. From `master`, port Blade/JS/CSS for viewer/print/zoom  
2. Wire download through `DocumentPolicy` + mother storage path  
3. No `ScholarshipProgram*`, no audit rename  
4. CHANGELOG + #58  
5. Open PR → CI green → CODEOWNER review

### Phase 3 — Observers PR (if Q08=A or C)
1. Map to `record_type` / `record_id` (+ payloads if Q06=C)  
2. Feature tests for create/update audit rows  
3. Link #56

### Phase 4 — Search (if Q09=A)
1. Blocked until Wakin ACK on `docs/db/METADATA_PRIMARY_KEYS.md`  
2. Separate PR

### Phase 5 — Close the loop
1. Convert #68 to **Draft** or close with comment “Superseded by #NN…”  
2. Update #58 checklist  
3. Append `planning/AGENTIC_CHANGELOG.md`

### Hard stops (abort automation)
- Merge conflicts unresolved after one rebase attempt → ask Wakin  
- Any absolute Windows path reintroduced  
- DomPDF or `files` table reappears  
- Choosing Q03/Q05/Q06/Q07/Q12 letter **B** without matching `CHAN_ACK`  
- CI red after two fix commits → ask Wakin

---

## Conflict hotspots (expect these files)

Agents should expect conflicts / double-maintenance in:

- `routes/web.php`  
- `app/Models/Document.php`, `DocumentVersion.php`, `Scholar.php`, `AuditLog.php`  
- `database/migrations/**` (prefer master history)  
- `database/seeders/**`  
- `app/Livewire/AddFile.php`, `Scholars/Edit.php`, `Scholars/Index.php`  
- `resources/views/livewire/dashboard/document-viewer.blade.php`  
- `CHANGELOG.md`, `planning/AGENTIC_CHANGELOG.md`

**Default conflict rule:** Q02=A → master wins for schema; re-apply viewer behavior manually.

---

## Definition of done (merge allowed)

- [ ] Q01–Q16 answered in required format  
- [ ] Required `CHAN_ACK` lines present for any non-default product choice  
- [ ] No merge conflicts vs `origin/master`  
- [ ] CI: test, pint, lint-css, synthetic-smoke green  
- [ ] No `ScholarshipProgram*` unless Chan ACK Q03=B  
- [ ] No flat `files` / DomPDF / absolute seed paths  
- [ ] Documents/audit match chosen Q05/Q06  
- [ ] CHANGELOG bullets with date-time + user  
- [ ] #68 closed or drafted; #58 updated  

---

## Wakin — please reply with

1. The `Q01:`…`Q16:` + `PIN_SHA:` block  
2. Anything you refuse to give up (folders, UUID docs, program naming) — call out explicitly so Chan can ACK or reject  
3. Preferred first PR: viewer (**Q10**) or observers (**Q08**)

Once that lands, agents can run the playbook without guessing.
