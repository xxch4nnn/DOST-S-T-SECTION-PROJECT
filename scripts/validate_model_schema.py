#!/usr/bin/env python3
import argparse
import hashlib
import json
from pathlib import Path

REQUIRED_MODEL_FIELDS = {
    "schema_version": str,
    "model_id": str,
    "version": str,
    "framework": str,
    "format": str,
    "path": str,
    "sha256": str,
    "metadata": dict,
}

def sha256_file(path: Path) -> str:
    h = hashlib.sha256()
    with path.open("rb") as f:
        for chunk in iter(lambda: f.read(1024 * 1024), b""):
            h.update(chunk)
    return h.hexdigest()

def validate_manifest(manifest_path: Path) -> list:
    errors = []
    try:
        data = json.loads(manifest_path.read_text(encoding="utf-8"))
    except Exception as e:
        return [f"Invalid JSON: {e}"]

    if data.get("schema_version") != "1":
        errors.append("schema_version must be '1'")

    for field, expected in REQUIRED_MODEL_FIELDS.items():
        if field not in data:
            errors.append(f"Missing required field: {field}")
        elif not isinstance(data[field], expected):
            errors.append(f"Field {field} must be {expected.__name__}")

    artifact = manifest_path.parent / data.get("path", "")
    metadata = data.get("metadata", {})
    is_placeholder = bool(metadata.get("placeholder"))

    if not artifact.exists():
        if not is_placeholder:
            errors.append(f"Artifact not found: {artifact}")
    else:
        actual = sha256_file(artifact)
        if actual != data.get("sha256"):
            errors.append(f"SHA256 mismatch: expected {data.get('sha256')}, got {actual}")

    if is_placeholder:
        print(f"Placeholder manifest detected: {manifest_path}")
    return errors

def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--manifest", required=True)
    args = parser.parse_args()

    manifest = Path(args.manifest).resolve()
    if not manifest.exists():
        print(f"Manifest not found: {manifest}")
        return 1

    errors = validate_manifest(manifest)
    if errors:
        print("Model manifest validation failed:")
        for error in errors:
            print(f"- {error}")
        return 1

    print(f"Model manifest OK: {manifest}")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())