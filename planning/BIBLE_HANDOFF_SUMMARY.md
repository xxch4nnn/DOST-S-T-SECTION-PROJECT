# DOSTorage V1 — Bible Keeper `TASKS DETECTED` Handoff Summary
**Date:** 2026-07-18  
**Chat duration:** Extended debugging session  
**Primary blocker:** Google Docs API cannot target secondary tabs from this Windows/Hermes client  
**Status at handoff:** Live-write mode restored in `bible_keeper.py`; `--tasks` currently writes to `Bible Center` by default  

---

## What Worked

| Item | Outcome |
|------|---------|
| `google-api-python-client` upgrade | ✅ `2.194.0` → `2.198.0` |
| OAuth re-auth with expanded scopes | ✅ Saved token now includes: `documents`, `drive`, `script.projects`, `script.deployments`, `cloud-platform` |
| Tab discovery via `includeTabsContent=True` | ✅ Returns all 9 tabs with correct IDs |
| CSV parser / schema guard / curation | ✅ Ingests all 5 `team_*.csv` files, curates 70 active + 0 archived tasks |
| Local export payload | ✅ `planning/TASKS_DETECTED_payload.md` contains complete 70-task markdown |
| Empty-tab boundary detection | ✅ `_find_tab_body_indexes` correctly resolves start/end for empty `TASKS DETECTED` |
| Hard safety gate | ✅ Requires `PROJECT_TASKS_TAB_ID=t.cm79ati3cwhz` environment variable |

---

## What Failed Repeatedly

| Approach | Failure Mode | Evidence |
|----------|--------------|----------|
| `batchUpdate` body with `tabId` | **Client rejected the field** or caused `NameError`/`HttpError 400` | `Got an unexpected keyword argument tabId`; later `tabId` was silently ignored |
| Writes to `TASKS DETECTED` via index math | **Always land in `Bible Center`** | Verified by readback: `Bible Center` gained Keeper content, `TASKS DETECTED` stayed empty |
| Unscoped writes believing default-tab targeting worked | **Default tab is `Bible Center`** | Confirmed via `documents().get()` without `includeTabsContent`: returns 0 tabs, all secondary tabs start at index 1 |
| Apps Script project creation | **403 `ACCESS_TOKEN_SCOPE_INSUFFICIENT`** | Root cause: `script.googleapis.com` API not enabled in Google Cloud project |
| `script.projects` / `script.deployments` calls | **403 even after re-auth** | Token has correct scopes; API itself is disabled in `drive-and-docs-access` project |

---

## Exact Current State

### Doc Structure
- **Doc ID:** `1TL6YADi71bi9fHAaF8YAypZWW-jCpDGkQvJosera-Ms`
- **Bible Center** (`t.kasj3s9yik6e`): Contains leaked Keeper content from multiple failed `--tasks` runs + probe writes
- **TASKS DETECTED** (`t.cm79ati3cwhz`): Empty (`chars=1`, just a newline)
- **Other tabs:** User Manual, Flowchart + DBMS, MVP, Meetings, Technical Documentation, Folder Structure, User Workflow

### Code State (`bible_keeper.py`)
- **Writer mode:** Restored live `batchUpdate` writes (not export-only)
- **Gate:** Requires `PROJECT_TASKS_TAB_ID` env var pointing to `t.cm79ati3cwhz`
- **Limit:** Writes tab-content bounds but **not** tab-scoped — all writes currently land in `Bible Center`
- **Writer path:** `_write_tasks_tab()` lines ~1217-1221 run live batchUpdate

### Exported Artifact
- **Path:** `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning\TASKS_DETECTED_payload.md`
- **Content:** 70 active tasks in bullet-list markdown, ready to paste into `TASKS DETECTED`

---

## Exact Next Steps (Do Not Skip)

### Step 1: Clean up `Bible Center` contamination (MANDATORY)
Undo all leaked Keeper content. Options:
1. **Docs UI version history** — restore `Bible Center` to state before first leaked write
2. **Manual edit** — delete the Keeper-generated sections in `Bible Center`
3. **Docs API** — delete ranges by structural element, but requires exact indexes

### Step 2: Decide on tab-write strategy (choose ONE)

**Option A: Enable Apps Script API**
1. Go to: `https://console.cloud.google.com/apis/library/script.googleapis.com?project=drive-and-docs-access`
2. Click **Enable** for Apps Script API
3. Wait 30–60 seconds
4. Create Apps Script bound to Bible doc with `writeToTab(tabId, text)` function
5. Deploy as API executable
6. Invoke from Python via `script.run()` endpoint
7. Update `_write_tasks_tab()` to call Apps Script instead of batchUpdate

**Option B: Manual paste (safest, zero risk)**
1. Open `planning/TASKS_DETECTED_payload.md`
2. Select all content, copy
3. Open `TASKS DETECTED` tab in Bible doc
4. Paste
5. Remove `--tasks` live-write mode or leave export-only

**Option C: Use Sheets as proxy**
1. Create new Google Sheet as task buffer
2. Write rows via Sheets API (fully supported, no tab issues)
3. View Sheet embedded in Bible doc or as separate view

**Option D: Upgrade `google-api-python-client` again later**
Future client releases may fix tab scoping. Re-run:
```bash
"C:\Users\Asus\AppData\Local\Programs\Python\Python311\python.exe" -m pip install --upgrade google-api-python-client
```
Then re-test `insertText` with newer discovery schema.

### Step 3: Once strategy chosen, update `_write_tasks_tab()`
- Change writer branch at lines ~1217-1221 in `bible_keeper.py`
- If export-only: keep payload print, remove batchUpdate
- If Apps Script: replace with HTTPS POST to deployed endpoint
- If manual: print `EXPORT_ONLY` instruction and exit 0

---

## Known Limitations

| Limitation | Workaround |
|------------|------------|
| This Windows/Hermes client writes unscoped docs updates to `Bible Center` | Use export-only, Apps Script, or Sheets proxy |
| `tabId` field is unsupported/ignored by installed `google-api-python-client` | Do not attempt tabId scoping |
| `script.googleapis.com` 403 despite valid token | Enable API in Google Cloud Console |
| No JSON output from `agy -p` | Parse stdout as plain text |

---

## Artifacts to Preserve

| File | Purpose |
|------|---------|
| `~/.hermes/skills/project-management/project-bible-keeper/scripts/bible_keeper.py` | Patched script with --tasks mode, safety gate, export mode |
| `~/.hermes/skills/project-management/project-bible-keeper/references/docs-client-tab-diagnosis.md` | Documents the tab scoping issue |
| `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning\TASKS_DETECTED_payload.md` | Ready-to-paste 70-task payload |
| `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\scripts\reauth_google_oauth.py` | Re-auth script with expanded scopes |
| `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\scripts\tasks_watcher.py` | Event-driven watcher daemon (untested endpoint dependency) |

---

## Recommended Path Forward

1. **Immediate:** Restore `Bible Center` from version history
2. **Immediate:** Paste exported payload into `TASKS DETECTED` manually
3. **Short-term:** Deploy Apps Script once `script.googleapis.com` is enabled
4. **Medium-term:** Monitor `google-api-python-client` for tab-scoping support
5. **Never:** Run `bible_keeper.py --tasks` with live writer until tab-scoped path is verified safe

---

*End of handoff summary*
