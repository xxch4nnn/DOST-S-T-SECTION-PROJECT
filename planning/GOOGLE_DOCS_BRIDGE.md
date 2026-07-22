# Google Docs / Drive Bridge — DOSTorage V1

Canon links for planning and artifacts. Slack is signals only; these remain the source of truth for docs.

## Confirmed canon

| Asset | URL | ID |
|---|---|---|
| Project Bible V0.2 | https://docs.google.com/document/d/1TL6YADi71bi9fHAaF8YAypZWW-jCpDGkQvJosera-Ms/edit?tab=t.7ua8hdut650u | `1TL6YADi71bi9fHAaF8YAypZWW-jCpDGkQvJosera-Ms` |
| DOST Development Drive | https://drive.google.com/drive/u/0/folders/1tWftnMoiXruD4as1qenQE7GFd_ypKyqo | `1tWftnMoiXruD4as1qenQE7GFd_ypKyqo` |

## Source-of-truth rules

| Layer | Owns |
|---|---|
| **Bible** (Google Doc) | Requirements, decisions, open floor / resolved items |
| **Drive** (folder) | Artifacts, exports, shared files |
| **GitHub** | Code, CI, repo planning markdown under `planning/` |
| **Slack** | Signals only (CI alerts, short pings) — not SoT |

## Hermes / local tooling

- Google token path: `C:\Users\Asus\AppData\Local\hermes\google_token.json`
- Bible Keeper (clone/archive-aware scanner): `.\scripts\bible_keeper.bat`
  - Do not duplicate Open Floor entries; keep Bible Center clean

## Pin list (for `#dostorage-pm`)

When Slack is ready, pin in `#dostorage-pm`:

1. Project Bible V0.2 (link above)
2. DOST Development Drive (link above)
3. This bridge doc in-repo: `planning/GOOGLE_DOCS_BRIDGE.md`

## Explicit skip

- **User Flowchart** Drive shortcut: skip unless verified working; do not treat as canon until confirmed.
