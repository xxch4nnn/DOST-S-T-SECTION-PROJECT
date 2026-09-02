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


def find_template_file(template_ref: str, contract_path: Path, root: Path) -> Path | None:
    direct = Path(template_ref).resolve()
    if direct.exists() and direct.is_file():
        return direct

    from_root = (root / template_ref).resolve()
    if from_root.exists() and from_root.is_file():
        return from_root

    ws_path = (Path.cwd() / template_ref).resolve()
    if ws_path.exists() and ws_path.is_file():
        return ws_path

    same_dir = (contract_path.parent / Path(template_ref).name).resolve()
    if same_dir.exists() and same_dir.is_file():
        return same_dir

    return None


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
    template = find_template_file(template_ref, contract_path, root)
    if not template or not template.exists():
        errors.append(f"Template not found: {template_ref}")
    elif template.stat().st_size == 0:
        errors.append(f"Template file is empty: {template}")

    return errors


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--contract", required=True)
    parser.add_argument("--root", default=".")
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