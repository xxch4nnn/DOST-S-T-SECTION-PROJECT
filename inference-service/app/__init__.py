from __future__ import annotations

from pathlib import Path
from typing import Any

from app.logging_setup import configure_logging
from app.main import app
from app.middleware import ObservabilityMiddleware, PredictLoggingMiddleware

configure_logging(log_path=Path("logs/inference.log"))


@app.on_event("startup")
async def on_startup() -> None:
    app.add_middleware(ObservabilityMiddleware)
    app.add_middleware(PredictLoggingMiddleware)


@app.get("/metrics")
async def metrics_endpoint() -> dict[str, Any]:
    return app.state.metrics.summary()


def create_app() -> Any:
    return app
