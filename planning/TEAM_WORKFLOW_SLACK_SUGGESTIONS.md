# TEAM_WORKFLOW — Slack suggestions (review-ready)

Do **not** merge these into `TEAM_WORKFLOW.md` until PM review / Adopt.

1. Route CI / pipeline alerts to `#dostorage-ci-alerts` (Incoming Webhook → `SLACK_CI_WEBHOOK_URL`).
2. Keep Bible + Drive pins in `#dostorage-pm`; link `planning/GOOGLE_DOCS_BRIDGE.md`.
3. Leave standups in-person (colocated team) unless fit test decides Adopt.
4. Do not treat Slack as SoT — Bible = requirements, Drive = artifacts, GitHub = code.
5. Defer Hermes daily standup / Bible→Slack auto-post until after Adopt.
6. Do not change hour-pool / burndown models for Slack.
7. Fit-test workflow: `.github/workflows/slack-fit-test.yml` only; leave `test.yml` alone until Adopt.
8. If Drop/Adjust: disable or delete `slack-fit-test.yml`; keep Google Docs bridge doc regardless.
