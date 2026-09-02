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
