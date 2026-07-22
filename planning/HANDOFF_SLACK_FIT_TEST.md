# Task — Slack Workflow Fit Test

## Scope
This is a **fit-test only**, not a full integrations rollout.  
Validate whether Slack can support the DOSTorage V1 team workflow before committing to deeper hooks.

## Assignee
You or a teammate with workspace admin permissions

## Timebox
60–90 minutes

---

## Objective
Get a minimal but realistic Slack workflow running end-to-end:
1. Workspace + channels created
2. CI alert path verified from GitHub to Slack
3. Standup/update posting path verified manually
4. Decide: keep, change, or abandon Slack as primary comms layer

---

## Step 1 — Workspace & Channels (15 min)

Create or confirm a workspace named `DOSTorage`.

Create these channels:
- `#dostorage-general`
- `#dostorage-dev`
- `#dostorage-pm`
- `#dostorage-ci-alerts`

Invite: Chan, Miguel, Rui, Wakin, Mushimuche.

Post one intro message in each channel like:
```
DOSTorage V1 test message — confirm you can see this channel.
```

Success criteria:
- All invited users can see all 4 channels
- No permission/workspace policy blocks

---

## Step 2 — GitHub → Slack CI Alert Path (20 min)

### Option A: Native GitHub App (recommended for fit test)
1. In Slack: Apps → Search “GitHub” → Install
2. Authorize the workspace
3. In GitHub repo: Settings → Webhooks → Add webhook
4. Payload URL: use Slack-provided URL from the GitHub app installation
5. Content type: `application/json`
6. Secret: leave default unless required
7. Events to subscribe:
   - `push`
   - `pull_request`
   - `check_suite`
8. Target channel in Slack: `#dostorage-ci-alerts`

### Option B: Incoming Webhook (simpler failover)
1. Slack: Apps → Incoming Webhooks → Create for `#dostorage-ci-alerts`
2. Copy webhook URL
3. GitHub repo: Settings → Secrets → Actions → New repo secret
   - Name: `SLACK_CI_WEBHOOK_URL`
   - Value: paste webhook URL
4. Add a temporary workflow file `.github/workflows/slack-test.yml`:

```yaml
name: Slack Fit Test
on:
  push:
    branches: [main]
  workflow_dispatch:

jobs:
  notify:
    runs-on: ubuntu-latest
    steps:
      - name: Notify Slack
        uses: slackapi/slack-github-action@v1.23.0
        with:
          channel-id: 'dostorage-ci-alerts'
          slack-message: |
            :white_check_mark: DOSTorage V1 Slack fit test — pipeline ran successfully.
        env:
          SLACK_BOT_TOKEN: ${{ secrets.SLACK_BOT_TOKEN }}
```

Note: this action may require a bot token. If it fails, use `curl` instead:

```yaml
      - name: Notify Slack via curl
        run: |
          curl -X POST -H 'Content-type: application/json' \
            --data '{"text":"DOSTorage V1 Slack fit test — pipeline ran successfully."}' \
            "$SLACK_CI_WEBHOOK_URL"
        env:
          SLACK_CI_WEBHOOK_URL: ${{ secrets.SLACK_CI_WEBHOOK_URL }}
```

Push to main and verify `#dostorage-ci-alerts` receives the message.

Success criteria:
- A commit or workflow run produces a visible message in `#dostorage-ci-alerts`
- Message arrives within 60 seconds

---

## Step 3 — Standup / Update Posting Path (15 min)

Manual test to validate the team’s real posting cadence.

In `#dostorage-dev`, post a standup-style update:

```
Standup test
Done: Bootstrap migration complete, tests green
Blocked: none
Next: ...
```

In `#dostorage-pm`, post a planning update:

```
Planning sync test
Doc: planning/long_range_planning.md updated
Decision: Laravel 13 + Livewire 3 + Bootstrap 5
Risk: none
```

Success criteria:
- Messages render correctly on mobile + desktop
- Threads/replies work if anyone needs to react or clarify
- No policy warnings about formatting or links

---

## Step 4 — Fit Evaluation (15 min)

Answer these in `planning/SLACK_FIT_TEST.md`:

| Question | Answer |
|---|---|
| Did all teammates receive and see messages? | Yes / No |
| Was CI alert path reliable? | Yes / No |
| Is posting cadence better than current chat? | Yes / No |
| What broke or was annoying? | ... |
| Decision | Adopt / Adjust / Drop |

---

## Exact Rollback / Stop Instructions

If Slack fit is bad:
1. Do not delete the workspace immediately; export channel list and members first
2. Document exact blockers in `planning/SLACK_FIT_TEST.md`
3. Do not enable deeper automations until blockers are resolved
4. Stop here; do not proceed to wider integrations

---

## Deliverable

One markdown file: `planning/SLACK_FIT_TEST.md`

Contents required:
- Channel list with member presence confirmed
- CI alert test evidence: timestamp + screenshot or message link
- Standup/post test notes
- Final fit decision
