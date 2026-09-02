#!/usr/bin/env python3
"""
Smoke test client for DOSTorage AIOps Inference Service.
Tests /health, /metrics, /predict, and /v1/validate-pr endpoints.
"""

from __future__ import annotations

import json
import sys
import urllib.error
import urllib.request


def make_request(url: str, method: str = "GET", data: dict | None = None) -> tuple[int, dict | str]:
    req = urllib.request.Request(url, method=method)
    if data is not None:
        req.add_header("Content-Type", "application/json")
        encoded = json.dumps(data).encode("utf-8")
    else:
        encoded = None

    try:
        with urllib.request.urlopen(req, data=encoded, timeout=10) as resp:
            body = resp.read().decode("utf-8")
            try:
                return resp.status, json.loads(body)
            except Exception:
                return resp.status, body
    except urllib.error.HTTPError as e:
        body = e.read().decode("utf-8")
        try:
            return e.code, json.loads(body)
        except Exception:
            return e.code, body


def main() -> int:
    base_url = "http://127.0.0.1:8080"
    if len(sys.argv) > 1:
        base_url = sys.argv[1].rstrip("/")

    print(f"Testing inference service at {base_url}...")

    # 1. Health check
    status, health = make_request(f"{base_url}/health")
    print(f"GET /health: {status} -> {health}")
    if status != 200:
        return 1

    # 2. Predict endpoint
    predict_payload = {
        "prompt_id": "scholar-record-classifier",
        "input": {"text": "DOST Scholar Record Clearance Certification"},
    }
    status, pred = make_request(f"{base_url}/predict", method="POST", data=predict_payload)
    print(f"POST /predict: {status} -> {pred}")
    if status != 200:
        return 1

    # 3. Validate PR endpoint
    val_payload = {
        "diff": "diff --git a/ai/eval/golden.jsonl b/ai/eval/golden.jsonl\n+{\"id\":\"test\"}\n",
        "allowed_paths": ["ai/*"],
        "forbidden_paths": ["routes/*"],
        "max_files": 5,
    }
    status, val = make_request(f"{base_url}/v1/validate-pr", method="POST", data=val_payload)
    print(f"POST /v1/validate-pr: {status} -> {val}")
    if status != 200:
        return 1

    # 4. Metrics endpoint
    status, metrics = make_request(f"{base_url}/metrics")
    print(f"GET /metrics: {status} -> {metrics}")
    if status != 200:
        return 1

    print("All smoke checks passed successfully.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
