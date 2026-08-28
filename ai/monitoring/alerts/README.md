# AIOps Alerting Rules

This directory contains alert definitions for the inference service and eval pipeline.

## Rule Format
Each rule file is JSON and must include:
- `schema_version`: "1"
- `rule_id`: stable identifier
- `severity`: "critical" | "warning" | "info"
- `condition`: expression or metric threshold
- `window`: evaluation window in seconds
- `action`: notification or automation trigger

## Example Rules
- High error rate on `/predict`
- Eval pass rate below threshold
- Model artifact missing or checksum mismatch
- Latency p95 above bound
