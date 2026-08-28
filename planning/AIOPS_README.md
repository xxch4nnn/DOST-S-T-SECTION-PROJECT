# AIOps / MLOps Readiness — DOSTorage V1

This file captures the operational and model-serving work needed to move from
current app-only CI toward an AIOps-capable delivery pipeline.

---

## Current State
- Application CI exists for lint/tests/build.
- There is no model registry, inference service, prompt pipeline runtime, or
  evaluation harness in the repo today.
- `ai/` contains planning docs only; no deployable model artifacts or serving
  code are present.

## Target State
- Every change affecting model/prompt/config files triggers AIOps checks.
- Model artifacts are versioned, validated, and optionally published to a
  registry or internal storage.
- Inference endpoints are deployment artifacts, not ad-hoc app code.

## Recommended AIOps Pipeline Stages
1. **Change detection** — model/prompt/config paths in PR trigger AIOps jobs.
2. **Validation** — schema validation, prompt safety checks, and contract tests.
3. **Build** — containerize inference service or export model package.
4. **Registry** — push artifact with immutable tag `commit-sha`.
5. **Deploy** — staging deploy with health and latency checks.
6. **Evaluate** — regression eval suite on golden dataset before production promotion.
7. **Monitor** — structured logs, metrics, and drift alerts.

## Immediate Actions
- Add secret/dep audit workflow: `.github/workflows/security.yml`
- Add model artifact directory conventions under `ai/models/` and `ai/prompts/`
- Add inference service scaffold in a separate service/container, not in web app
- Add evaluation harness with pinned golden dataset and pass/fail thresholds

---

*Maintainer: AIOps/DevOps owner*
*Review cycle: per model or prompt change*
