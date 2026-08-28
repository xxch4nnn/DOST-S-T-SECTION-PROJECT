# Model Artifact Conventions

Models are treated as versioned deployable artifacts, not app code.

## Directory Layout
- `ai/models/<model_id>/<version>/manifest.json`
- optional `ai/models/<model_id>/<version>/artifact.*`

- `ai/models/schema.json` — shared schema example, not a versioned manifest

## Manifest Contract
Required fields:
- `schema_version`: "1"
- `model_id`: stable identifier
- `version`: semver or commit-based tag
- `framework`: e.g. "transformers", "gguf", "onnx"
- `format`: e.g. "safetensors", "gguf", "onnx"
- `path`: relative artifact path from manifest directory
- `sha256`: artifact digest
- `metadata`: optional key/value labels
- `metadata.placeholder`: optional boolean for scaffold-only manifests without real artifacts

## Validation
Run `scripts/validate_model_schema.py --manifest <manifest.json>` in CI before publishing.

## Naming
Use lowercase, hyphen-separated model IDs.
