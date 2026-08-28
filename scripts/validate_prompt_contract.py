#!/usr/bin/env python3
import argparse
import json
from pathlib import Path

REQUIRED_PROMPT_FIELDS = {
    "schema_version": str,
    "prompt_id": str,
    "version": str,
    "template_ref": str,
    "input_schema": dict,
    "output_schema": dict,
    "safety_rules": list,
}

def validate_prompt(contract_path: Path, root: Path) -> list:
    errors = []
    try:
        data = json.loads(contract_path.read_text(encoding="utf-8"))
    except Exception as e:
        return [f"Invalid JSON: {e}"]

    if data.get("schema_version") != "1":
        errors.append("schema_version must be '1'")

    for field, expected in REQUIRED_PROMPT_FIELDS.items():
        if field not in data:
            errors.append(f"Missing required field: {field}")
        elif not isinstance(data[field], expected):
            errors.append(f"Field {field} must be {expected.__name__}")

    template_ref = data.get("template_ref", "")
    template = (root / template_ref).resolve()
    if not template.exists():
        template = Path(template_ref).resolve()
    if not template.exists():
        errors.append(f"Template not found: {template}")
    elif template.stat().st_size == 0:
        errors.append("Template file is empty")

    return errors

def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--contract", required=True)
    parser.add_argument("--root", default="ai/prompts")
    args = parser.parse_args()

    contract = Path(args.contract).resolve()
    root = Path(args.root).resolve()

    if not contract.exists():
        print(f"Contract not found: {contract}")
        return 1

    errors = validate_prompt(contract, root)
    if errors:
        print("Prompt contract validation failed:")
        for error in errors:
            print(f"- {error}")
        return 1

    print(f"Prompt contract OK: {contract}")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())