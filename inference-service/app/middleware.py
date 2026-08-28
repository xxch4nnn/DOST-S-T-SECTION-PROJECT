from __future__ import annotations

import logging
import time
from typing import Any

from fastapi import Request
from starlette.middleware.base import BaseHTTPMiddleware

from app.logging_setup import log_event
from app.metrics import metrics

logger = logging.getLogger("inference")


class ObservabilityMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):  # type: ignore[override]
        start = time.perf_counter()
        trace_id = request.headers.get("x-trace-id", "")
        span_id = request.headers.get("x-span-id", "")

        response = await call_next(request)
        latency_ms = (time.perf_counter() - start) * 1000
        status = "success" if response.status_code < 400 else "error"
        metrics.record(latency_ms=latency_ms, status=status)

        logger.info(
            "%s %s %s %s %.2fms",
            request.method,
            request.url.path,
            response.status_code,
            status,
            latency_ms,
            extra={
                "event": "http_request",
                "trace_id": trace_id,
                "span_id": span_id,
                "metadata": {
                    "method": request.method,
                    "path": request.url.path,
                    "status_code": response.status_code,
                    "latency_ms": latency_ms,
                },
            },
        )

        response.headers["x-trace-id"] = trace_id
        response.headers["x-span-id"] = span_id
        return response


class PredictLoggingMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):  # type: ignore[override]
        if request.url.path != "/predict":
            return await call_next(request)

        body = await request.json()
        start = time.perf_counter()
        response = await call_next(request)
        latency_ms = (time.perf_counter() - start) * 1000

        try:
            payload = await response.json()
        except Exception:
            payload = {}

        log_event(
            "predict",
            trace_id=request.headers.get("x-trace-id", ""),
            span_id=request.headers.get("x-span-id", ""),
            prompt_id=body.get("prompt_id"),
            prompt_version=body.get("version"),
            status="success" if response.status_code < 400 else "error",
            latency_ms=latency_ms,
            output=payload.get("output"),
            error=payload.get("error"),
        )

        return response
