# Slack Fit Test — Evaluation

Lean fit test (channels + CI webhook + Doc pins). Not a full Slack PM rollout.

Date: 2026-07-22  
Repo: `xxch4nnn/DOST-S-T-SECTION-PROJECT` (default branch `master`)  
Last probe: 2026-07-22 ~1:53 PM (UTC+8)

---

## Blockers / Pending human

| # | Human action | Status |
|---|---|---|
| 1 | Slack workspace + `#dostorage-*` channels + invites | **You said done** — confirm in Slack UI (Hermes still cannot see them) |
| 2 | Incoming Webhook on `#dostorage-ci-alerts` | Likely created (secret exists) |
| 3 | GitHub secret `SLACK_CI_WEBHOOK_URL` | **Present** (`gh secret list`) but **value is malformed** |
| 4 | Optional: connect Hermes → Slack | **Not connected** — `channels_list` still `count: 0` |

### CI smoke result (2026-07-22)

- Pushed commit `8c3bd8d` + dispatched `Slack Fit Test`
- Runs: [29895043746](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/actions/runs/29895043746), [29895044671](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/actions/runs/29895044671)
- Both **failed**: `curl: (3) URL rejected: Malformed input to a URL function`
- Secret is **set** (not empty) but is **not a valid URL** as stored — usually means quotes, spaces, a newline, or a non-webhook string was pasted

### Exact next human steps (priority order)

1. **Fix the secret** (required for CI path):
   - Slack → Apps → Incoming Webhooks → copy the URL for `#dostorage-ci-alerts`
   - It must look like: `https://hooks.slack.com/services/T.../B.../...`
   - GitHub → Settings → Secrets → Actions → `SLACK_CI_WEBHOOK_URL` → **Update**
   - Paste **only** the URL — no quotes, no spaces, no backticks
2. Re-run: `gh workflow run "Slack Fit Test" --ref master` (or ask Cursor to re-dispatch)
3. Confirm message appears in `#dostorage-ci-alerts` within ~60s
4. In `#dostorage-pm`, pin:
   - [Project Bible V0.2](https://docs.google.com/document/d/1TL6YADi71bi9fHAaF8YAypZWW-jCpDGkQvJosera-Ms/edit?tab=t.7ua8hdut650u)
   - [DOST Development Drive](https://drive.google.com/drive/u/0/folders/1tWftnMoiXruD4as1qenQE7GFd_ypKyqo)
5. **Optional Hermes Slack connect** (so agents can post/pin for you):
   - In Hermes, add/connect the Slack workspace the same way other messaging platforms are linked
   - After connect, Cursor can assign Hermes tasks below

---

## Hermes tasks (once Slack is connected)

Hermes currently cannot do these (`channels_list` empty). After connect:

| Task | Target | Message / action |
|---|---|---|
| H1 | `#dostorage-general` | Intro: `DOSTorage V1 test — confirm you can see this channel.` |
| H2 | `#dostorage-dev` | Same intro |
| H3 | `#dostorage-pm` | Same intro + Bible + Drive links from `GOOGLE_DOCS_BRIDGE.md` |
| H4 | `#dostorage-ci-alerts` | Confirm channel reachable: `Hermes Slack bridge smoke — channel OK.` |
| H5 | `#dostorage-pm` | Ask human to **Pin** the Bible + Drive message (Hermes can post; pin is usually human) |

Until Hermes↔Slack is linked, do H1–H5 yourself in Slack (2 minutes).

---

## Probe evidence (agent)

| Check | Result |
|---|---|
| Hermes Slack channels | `count: 0` |
| `SLACK_CI_WEBHOOK_URL` secret | Listed in `gh secret list` (updated 05:50 UTC) |
| Workflow on remote | Active (`Slack Fit Test`) |
| Smoke curl | **Failed** — malformed URL in secret |
| `gh auth` | OK |

---

## Handoff questions

| Question | Answer |
|---|---|
| Did all teammates receive and see messages? | **Unknown** — no successful bot/CI message yet |
| Was CI alert path reliable? | **Not yet** — secret malformed; curl rejected URL |
| Is posting cadence better than current chat? | **Not evaluated** |
| What broke or was annoying? | Webhook secret value invalid for curl; Hermes not linked to Slack |
| Smoke CI webhook message landed? | **No** |
| Bible + Drive pinned in `#dostorage-pm`? | **No** (do manually or via Hermes after connect) |

---

## Decision

**Pending** (one blocker left: fix webhook secret, then re-smoke).

| Option | When |
|---|---|
| **Adopt** | After verified CI message in `#dostorage-ci-alerts` + Bible/Drive visible in `#dostorage-pm` |
| **Adjust** | Channels OK but webhook/Hermes path needs tweaks |
| **Drop** | Prefer current chat; delete/disable `slack-fit-test.yml`; keep `GOOGLE_DOCS_BRIDGE.md` |

---

## Artifact checklist

- [x] `.github/workflows/slack-fit-test.yml` (pushed `8c3bd8d`; trim/validate URL hardening pending push)
- [x] `planning/GOOGLE_DOCS_BRIDGE.md`
- [x] `planning/SLACK_CHANNEL_MAP.md`
- [x] `planning/SLACK_FIT_TEST.md` (this file)
- [x] `planning/TEAM_WORKFLOW_SLACK_SUGGESTIONS.md`
- [ ] CI smoke message in `#dostorage-ci-alerts`
- [ ] Bible + Drive pins in `#dostorage-pm`
