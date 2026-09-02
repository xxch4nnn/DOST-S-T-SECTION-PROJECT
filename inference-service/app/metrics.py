from __future__ import annotations

import json
import threading
import time
from collections import deque
from dataclasses import dataclass, field
from typing import Any, Deque, Dict


@dataclass
class RequestMetrics:
    timestamp: float = field(default_factory=time.time)
    latency_ms: float = 0.0
    status: str = "success"
    error: str | None = None


class MetricsStore:
    def __init__(self, max_samples: int = 1000) -> None:
        self._lock = threading.Lock()
        self._samples: Deque[RequestMetrics] = deque(maxlen=max_samples)

    def record(self, latency_ms: float, status: str, error: str | None = None) -> None:
        with self._lock:
            self._samples.append(
                RequestMetrics(
                    timestamp=time.time(),
                    latency_ms=latency_ms,
                    status=status,
                    error=error,
                )
            )

    def summary(self) -> Dict[str, Any]:
        with self._lock:
            samples = list(self._samples)

        count = len(samples)
        errors = sum(1 for s in samples if s.status != "success")
        latencies = [s.latency_ms for s in samples]
        latencies_sorted = sorted(latencies)
        p95 = latencies_sorted[int(count * 0.95)] if count else 0.0

        return {
            "count": count,
            "errors": errors,
            "error_rate": errors / count if count else 0.0,
            "p95_latency_ms": p95,
            "max_latency_ms": max(latencies) if latencies else 0.0,
        }


metrics = MetricsStore()
