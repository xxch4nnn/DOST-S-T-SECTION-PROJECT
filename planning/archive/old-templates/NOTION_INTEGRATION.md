# Notion Integration Guide — DOSTorage V1

## Scope
This document describes how to integrate the DOSTorage V1 Bible Center with Notion for read-only project status mirroring. Notion remains the primary human editing surface; the Bible remains the primary AIOps execution surface.

## Prerequisites
- Notion workspace with the `DOST OJT Digitalization Project` database exported
- Notion integration token with access to the target database
- Python package: `notion-client` or `requests`

## Step 1 — Create a Notion Integration
1. Go to https://www.notion.so/profile/integrations
2. Click “New integration”
3. Name: `DOSTorage AIOps Bridge`
4. Select the workspace containing the project database
5. Copy the `Internal Integration Token`

## Step 2 — Share the Database with the Integration
1. Open the `DOST OJT Digitalization Project` database in Notion
2. Click the `...` menu → “Add connections”
3. Search for `DOSTorage AIOps Bridge` and confirm

## Step 3 — Map Fields
Use this mapping between Bible task rows and Notion properties:

| Bible column | Notion property | Type |
|--------------|-----------------|------|
| Task ID | `Task name` prefix `[ID]` | Title |
| Description | `Task name` main text | Title |
| Owner | `Assignee` | Person / text |
| Hours | custom property `Hours` | Number |
| Status | `Status` | Status |
| Dependencies | `Description` appendix | Text |
| Detected | custom property `Detected` | Date / text |
| Priority | `Priority` | Select |

## Step 4 — Read-Only Mirror Logic
The Keeper will:
1. Fetch all pages from the Notion database
2. Build a map: Task ID → Notion status
3. Overlay Notion status onto Bible tasks when writing `TASKS DETECTED`

Concrete overlay rules:
- If a task exists in both Bible CSV and Notion, Bible shows `CSV Status (Notion: NotionStatus)`
- If a task exists only in Notion, it is skipped
- Notion writes are never performed by the Keeper

## Step 5 — Optional: Env Config
Add to `~/.hermes/.env`:
```
NOTION_TOKEN=secret_...
NOTION_DATABASE_ID=...
```

The Keeper will detect these and enable Notion mirroring automatically. If absent, it falls back to local-file status only.

## Step 6 — Verification
1. Run `bible_keeper.py --tasks --dry-run` to preview overlay
2. Confirm task counts match Notion active filters
3. Write to Bible manually once before enabling cron

## Future Enhancements
- Notion → Bible status writeback (requires explicit confirmation mode)
- Bidirectional sync with conflict detection
- Notion webhook listener for real-time updates
