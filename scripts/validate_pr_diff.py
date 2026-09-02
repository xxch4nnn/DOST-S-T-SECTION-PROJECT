#!/usr/bin/env python3
"""
AIOps PR Diff Validator
Validates pull request diffs against path whitelist/blacklist rules and file limits.
Outputs JSON compliant with ai/PROMPTS/validate-edit-pr/0.1.0/prompt.json schema.
"""

from __future__ import annotations

import argparse
import fnmatch
import json
import subprocess
import sys
from pathlib import Path
from typing import Any, Dict, List, Optional, Set


def parse_diff_paths(diff_text: str) -> List[str]:
    """Extract all modified, added, renamed, or deleted file paths from a unified git diff."""
    paths: Set[str] = set()
    for line in diff_text.splitlines():
        line = line.strip()
        if line.startswith("diff --git"):
            parts = line.split()
            if len(parts) >= 4:
                b_path = parts[3]
                if b_path.startswith("b/"):
                    b_path = b_path[2:]
                paths.add(b_path)
                a_path = parts[2]
                if a_path.startswith("a/"):
                    a_path = a_path[2:]
                paths.add(a_path)
        elif line.startswith("+++ b/"):
            path = line[6:].strip()
            if path and path != "/dev/null":
                paths.add(path)
        elif line.startswith("--- a/"):
            path = line[6:].strip()
            if path and path != "/dev/null":
                paths.add(path)
        elif line.startswith("rename to "):
            path = line[10:].strip()
            if path:
                paths.add(path)
        elif line.startswith("rename from "):
            path = line[12:].strip()
            if path:
                paths.add(path)

    # Filter out dev/null placeholder
    paths.discard("/dev/null")
    return sorted(list(paths))


def check_path_matches(path: str, patterns: List[str]) -> bool:
    """Check whether a relative path matches any pattern or directory prefix."""
    normalized_path = path.replace("\\", "/")
    for pattern in patterns:
        pat = pattern.replace("\\", "/").strip()
        if not pat:
            continue
        if pat == "*" or pat == "**":
            return True
        prefix = pat.rstrip("/*")
        if normalized_path == prefix or normalized_path.startswith(f"{prefix}/"):
            return True
        if fnmatch.fnmatch(normalized_path, pat) or fnmatch.fnmatch(normalized_path, f"**/{pat}"):
            return True
    return False


def validate_diff(
    diff_text: str,
    allowed_paths: Optional[List[str]] = None,
    forbidden_paths: Optional[List[str]] = None,
    max_files: Optional[int] = None,
) -> Dict[str, Any]:
    """Validate diff contents against safety constraints and output structured verdict."""
    allowed_paths = allowed_paths or []
    forbidden_paths = forbidden_paths or []

    diff_text = diff_text.strip()
    if not diff_text:
        return {
            "valid": True,
            "violations": [],
            "summary": "Empty diff provided. Validation passed trivially.",
        }

    changed_files = parse_diff_paths(diff_text)
    violations: List[Dict[str, str]] = []

    if max_files is not None and len(changed_files) > max_files:
        violations.append({
            "path": "[PR_SCOPE]",
            "reason": f"Changed files count ({len(changed_files)}) exceeds max allowed limit ({max_files}).",
        })

    for path in changed_files:
        if forbidden_paths and check_path_matches(path, forbidden_paths):
            violations.append({
                "path": path,
                "reason": f"Modified file matches forbidden path policy: {path}",
            })
        elif allowed_paths and not check_path_matches(path, allowed_paths):
            violations.append({
                "path": path,
                "reason": f"Modified file is outside allowed path whitelist: {path}",
            })

    valid = len(violations) == 0
    if valid:
        summary = f"PR diff complies with all {len(allowed_paths) + len(forbidden_paths)} policy rule(s). Checked {len(changed_files)} file(s)."
    else:
        summary = f"PR diff violates safety policies with {len(violations)} violation(s) across {len(changed_files)} file(s)."

    return {
        "valid": valid,
        "violations": violations,
        "summary": summary,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description="AIOps PR Diff Validator")
    parser.add_argument("--diff-file", help="Path to unified diff / patch file")
    parser.add_argument("--diff-text", help="Raw diff string")
    parser.add_argument("--git-range", help="Git revision range (e.g., origin/master...HEAD)")
    parser.add_argument("--allowed-paths", help="Comma-separated list of allowed path globs")
    parser.add_argument("--forbidden-paths", help="Comma-separated list of forbidden path globs")
    parser.add_argument("--max-files", type=int, default=None, help="Maximum allowed number of changed files")
    parser.add_argument("--output", help="Optional output JSON file path")
    args = parser.parse_args()

    diff_content = ""
    if args.diff_text:
        diff_content = args.diff_text
    elif args.diff_file:
        diff_path = Path(args.diff_file)
        if not diff_path.exists():
            print(f"Error: diff file '{args.diff_file}' not found", file=sys.stderr)
            return 1
        diff_content = diff_path.read_text(encoding="utf-8", errors="replace")
    elif args.git_range:
        try:
            res = subprocess.run(
                ["git", "diff", args.git_range],
                capture_output=True,
                text=True,
                check=True,
            )
            diff_content = res.stdout
        except Exception as e:
            print(f"Error executing git diff for {args.git_range}: {e}", file=sys.stderr)
            return 1
    elif not sys.stdin.isatty():
        diff_content = sys.stdin.read()
    else:
        print("Error: Must provide --diff-file, --diff-text, --git-range, or pipe diff via stdin.", file=sys.stderr)
        return 1

    allowed = [p.strip() for p in args.allowed_paths.split(",") if p.strip()] if args.allowed_paths else []
    forbidden = [p.strip() for p in args.forbidden_paths.split(",") if p.strip()] if args.forbidden_paths else []

    result = validate_diff(
        diff_text=diff_content,
        allowed_paths=allowed,
        forbidden_paths=forbidden,
        max_files=args.max_files,
    )

    output_json = json.dumps(result, indent=2)
    if args.output:
        Path(args.output).write_text(output_json, encoding="utf-8")

    print(output_json)
    return 0 if result["valid"] else 1


if __name__ == "__main__":
    raise SystemExit(main())
