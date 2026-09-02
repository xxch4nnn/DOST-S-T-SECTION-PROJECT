from __future__ import annotations

import json
import logging
import time
from pathlib import Path
from typing import Any

LOG_RECORD_FACTORY = logging.getLogRecordFactory()


def structured_record_factory(*args: Any, **kwargs: Any) -> logging.LogRecord:
    record = LOG_RECORD_FACTORY(*args, **kwargs)
    record.structured = {
        "schema_version": "1",
        "level": record.levelname.lower(),
        "service": "inference",
        "event": getattr(record, "event", "message"),
        "timestamp": record.created,
        "trace_id": getattr(record, "trace_id", None),
        "span_id": getattr(record, "span_id", None),
        "message": record.getMessage(),
        "metadata": getattr(record, "metadata", {}),
    }
    return record


def configure_logging(log_path: Path | None = None) -> None:
    logging.setLogRecordFactory(structured_record_factory)
    logger = logging.getLogger()
    logger.setLevel(logging.INFO)

    formatter = logging.Formatter("%(message)s")

    console = logging.StreamHandler()
    console.setFormatter(formatter)
    logger.addHandler(console)

    if log_path:
        log_path.parent.mkdir(parents=True, exist_ok=True)
        file_handler = logging.FileHandler(log_path, encoding="utf-8")
        file_handler.setFormatter(formatter)
        logger.addHandler(file_handler)


def log_event(event: str, **metadata: Any) -> None:
    logger = logging.getLogger("inference")
    extra = {
        "event": event,
        "metadata": metadata,
    }
    logger.info("", extra=extra)
