# AIOps Monitoring & Logging Conventions

This directory contains monitoring and logging standards for AIOps artifacts and the inference service.

## Structured Log Schema
All AIOps logs should follow the schema in `schema.json`.

## Key Events
- `model_load` — model artifact loaded into memory or service
- `predict` — inference request processed
- `eval_run` — evaluation job started/completed
- `artifact_validation` — model/prompt validation executed

## Metrics
- Request count, error count, latency histogram
- Model version in use
- Eval pass/fail rate over time

## Retention
- JSON logs: 30 days minimum
- Metrics: 90 days minimum
