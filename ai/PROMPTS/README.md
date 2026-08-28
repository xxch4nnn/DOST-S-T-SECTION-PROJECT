# Prompt Artifact Conventions

Prompts are versioned contracts with explicit input/output schemas and safety rules.

## Directory Layout
- `ai/prompts/<prompt_id>/<version>/prompt.json`
- `ai/prompts/<prompt_id>/<version>/template.txt`

- `ai/prompts/schema.json` — shared schema example, not a versioned prompt artifact

## Contract Fields
Required fields:
- `schema_version`: "1"
- `prompt_id`: stable identifier
- `version`: semver or date tag
- `template_ref`: template path relative to `ai/prompts/`
- `input_schema`: JSON Schema subset
- `output_schema`: JSON Schema subset
- `safety_rules`: array of constraints
- `tags`: optional labels

## Validation
Run `scripts/validate_prompt_contract.py --contract <prompt.json>` in CI before deploy.
