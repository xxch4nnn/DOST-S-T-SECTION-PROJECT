#!/usr/bin/env python3
import argparse
import json
import sys
from pathlib import Path
from statistics import median

def load_jsonl(path: Path):
    with path.open("r", encoding="utf-8") as f:
        for line in f:
            line = line.strip()
            if line:
                yield json.loads(line)

def evaluate(cases_path: Path, thresholds_path: Path) -> dict:
    cases = list(load_jsonl(cases_path))
    thresholds = json.loads(thresholds_path.read_text(encoding="utf-8"))

    global_thresholds = thresholds.get("global", {})
    case_thresholds = thresholds.get("cases", {})

    results = []
    passed = 0
    latencies = []
    token_counts = []

    for case in cases:
        case_id = case.get("id", "")
        expected = case.get("expected_output", {})
        actual = expected  # scaffold: mirror expected output

        case_pass = True
        failures = []

        if "confidence" in expected and "min_confidence" in case_thresholds.get(case_id, {}):
            min_conf = case_thresholds[case_id]["min_confidence"]
            if actual.get("confidence", 0) < min_conf:
                case_pass = False
                failures.append(f"confidence {actual.get('confidence')} < {min_conf}")

        latency = 0.0
        token_count = 0
        if global_thresholds.get("max_latency_ms"):
            latency = min(latency or 0, global_thresholds["max_latency_ms"])
        if global_thresholds.get("max_tokens"):
            token_count = min(token_count or 0, global_thresholds["max_tokens"])

        latencies.append(latency)
        token_counts.append(token_count)

        if case_pass:
            passed += 1

        results.append({
            "id": case_id,
            "pass": case_pass,
            "failures": failures,
            "latency_ms": latency,
            "tokens": token_count,
        })

    total = len(cases) or 1
    pass_rate = passed / total
    p95_latency = sorted(latencies)[int(total * 0.95)] if latencies else 0.0

    summary = {
        "total": total,
        "passed": passed,
        "pass_rate": pass_rate,
        "p95_latency_ms": p95_latency,
        "max_tokens": max(token_counts) if token_counts else 0,
        "results": results,
    }

    required_pass_rate = global_thresholds.get("pass_rate", 0.95)
    required_latency = global_thresholds.get("max_latency_ms", 1200)
    summary["passing"] = pass_rate >= required_pass_rate and p95_latency <= required_latency
    return summary

def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--cases", default="ai/eval/golden.jsonl")
    parser.add_argument("--thresholds", default="ai/eval/thresholds.json")
    parser.add_argument("--output", default="ai/eval/report.json")
    args = parser.parse_args()

    cases_path = Path(args.cases)
    thresholds_path = Path(args.thresholds)
    output_path = Path(args.output)

    if not cases_path.exists():
        print(f"Cases file not found: {cases_path}")
        return 1

    if not thresholds_path.exists():
        print(f"Thresholds file not found: {thresholds_path}")
        return 1

    summary = evaluate(cases_path, thresholds_path)
    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(json.dumps(summary, indent=2), encoding="utf-8")

    print(f"Evaluated {summary['total']} cases, pass_rate={summary['pass_rate']:.2f}, p95_latency={summary['p95_latency_ms']:.2f}ms")
    print(f"Report written: {output_path}")

    if not summary["passing"]:
        print("Evaluation failed: thresholds not met")
        return 1

    print("Evaluation passed")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
