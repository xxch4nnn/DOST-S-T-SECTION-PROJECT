# Evaluation Conventions

Golden datasets are versioned JSONL files with expected outputs and tags.

## File Layout
- `ai/eval/golden.jsonl`
- `ai/eval/thresholds.json`
- `ai/eval/<prompt_id_or_model_id>.jsonl`

## Golden Record Fields
- `id`: unique case id
- `prompt_id`: linked prompt artifact id
- `model_id`: linked model artifact id or null
- `input`: request payload
- `expected_output`: contract-valid expected output
- `tags`: optional scenario tags
- `created_at`: ISO timestamp

## Thresholds
- `pass_rate`: minimum fraction of passing cases
- `max_latency_ms`: p95 latency bound
- `max_tokens`: optional output length bound
- `allowed_scores`: optional metric-specific bounds

## Usage
Run evaluator in CI and gate release on pass/fail.
