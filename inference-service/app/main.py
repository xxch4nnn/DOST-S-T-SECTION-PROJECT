from __future__ import annotations

import fnmatch
import time
from typing import Any, List, Optional
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field

from app.logging_setup import configure_logging, log_event
from app.metrics import metrics
from app.middleware import ObservabilityMiddleware

configure_logging()

app = FastAPI(
    title="DOSTorage Inference Service",
    description="AIOps Inference and PR Validation Microservice for DOSTorage V1",
    version="0.1.0",
)

app.add_middleware(ObservabilityMiddleware)


class PredictRequest(BaseModel):
    prompt_id: str
    version: Optional[str] = None
    input: dict


class PredictResponse(BaseModel):
    prompt_id: str
    version: str
    output: dict
    latency_ms: float


class PRViolation(BaseModel):
    path: str
    reason: str


class ValidatePRRequest(BaseModel):
    diff: str = Field(..., description="Git diff or patch text for the PR")
    allowed_paths: List[str] = Field(default_factory=list, description="List of allowed glob/path prefixes")
    forbidden_paths: List[str] = Field(default_factory=list, description="List of forbidden glob/path prefixes")
    max_files: Optional[int] = Field(default=None, ge=1, description="Maximum allowed changed files")


class ValidatePRResponse(BaseModel):
    valid: bool
    violations: List[PRViolation]
    summary: str


def parse_diff_paths(diff_text: str) -> List[str]:
    paths = set()
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
            paths.add(line[6:].strip())
        elif line.startswith("--- a/"):
            a_path = line[6:].strip()
            if a_path != "/dev/null":
                paths.add(a_path)
    paths.discard("/dev/null")
    return sorted(list(paths))


def check_path_match(path: str, patterns: List[str]) -> bool:
    normalized = path.replace("\\", "/")
    for pattern in patterns:
        pat = pattern.replace("\\", "/").strip()
        if not pat:
            continue
        if pat == "*" or pat == "**":
            return True
        prefix = pat.rstrip("/*")
        if normalized == prefix or normalized.startswith(f"{prefix}/"):
            return True
        if fnmatch.fnmatch(normalized, pat) or fnmatch.fnmatch(normalized, f"**/{pat}"):
            return True
    return False


def execute_validate_pr(payload: ValidatePRRequest) -> ValidatePRResponse:
    diff_text = payload.diff.strip()
    if not diff_text:
        return ValidatePRResponse(
            valid=True,
            violations=[],
            summary="Empty diff provided. Validation passed trivially."
        )

    changed_files = parse_diff_paths(diff_text)
    violations: List[PRViolation] = []

    if payload.max_files is not None and len(changed_files) > payload.max_files:
        violations.append(
            PRViolation(
                path="[PR_SCOPE]",
                reason=f"Exceeded maximum allowed files changed: {len(changed_files)} > {payload.max_files}"
            )
        )

    for path in changed_files:
        if payload.forbidden_paths and check_path_match(path, payload.forbidden_paths):
            violations.append(
                PRViolation(
                    path=path,
                    reason=f"Touched forbidden path matching policy: {path}"
                )
            )
        elif payload.allowed_paths and not check_path_match(path, payload.allowed_paths):
            violations.append(
                PRViolation(
                    path=path,
                    reason=f"File path is outside allowed paths whitelist: {path}"
                )
            )

    valid = len(violations) == 0
    if valid:
        summary = f"PR complies with all policies. Checked {len(changed_files)} changed file(s)."
    else:
        summary = f"PR violates policy with {len(violations)} violation(s) across {len(changed_files)} changed file(s)."

    return ValidatePRResponse(valid=valid, violations=violations, summary=summary)


@app.get("/health")
async def health() -> dict:
    return {"status": "ok", "service": "inference"}


@app.get("/metrics")
async def get_metrics() -> dict:
    return metrics.summary()


@app.post("/v1/validate-pr", response_model=ValidatePRResponse)
async def validate_pr_endpoint(request: ValidatePRRequest) -> ValidatePRResponse:
    start = time.perf_counter()
    result = execute_validate_pr(request)
    latency_ms = (time.perf_counter() - start) * 1000
    log_event(
        "validate_pr",
        valid=result.valid,
        violations_count=len(result.violations),
        latency_ms=latency_ms,
    )
    return result


@app.post("/predict", response_model=PredictResponse)
async def predict(request: PredictRequest) -> PredictResponse:
    start = time.perf_counter()

    if request.prompt_id == "validate-edit-pr":
        try:
            req_data = ValidatePRRequest(**request.input)
            val_resp = execute_validate_pr(req_data)
            output = val_resp.model_dump()
        except Exception as e:
            raise HTTPException(status_code=400, detail=f"Invalid validate-edit-pr input: {e}")
    elif request.prompt_id == "scholar-record-classifier":
        text = str(request.input.get("text", "")).lower()
        if "scholar" in text or "dost" in text or "clearance" in text:
            output = {"label": "high", "confidence": 0.95}
        elif "admin" in text or "record" in text:
            output = {"label": "medium", "confidence": 0.86}
        else:
            output = {"label": "low", "confidence": 0.50}
    else:
        output = {"label": "unknown", "confidence": 0.0}

    latency_ms = (time.perf_counter() - start) * 1000

    log_event(
        "predict",
        prompt_id=request.prompt_id,
        version=request.version or "0.1.0",
        latency_ms=latency_ms,
        output=output,
    )

    return PredictResponse(
        prompt_id=request.prompt_id,
        version=request.version or "0.1.0",
        output=output,
        latency_ms=latency_ms,
    )
