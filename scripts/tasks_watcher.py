#!/usr/bin/env python3
"""Event-driven watcher for TASKS DETECTED tab.

Watches planning/ for team_*.csv and dostorage-v1-project-checklist.md changes.
Debounces within a 10-second window, then runs: python bible_keeper.py --tasks
Optionally sends webhook notifications and chains Gantt generation.
"""

import json
import os
import subprocess
import sys
import time
from datetime import datetime
from datetime import timezone
from pathlib import Path
from urllib import request as urllib_request

from watchdog.events import FileSystemEvent, FileSystemEventHandler
from watchdog.observers.polling import PollingObserver

PLANNING_DIR = Path(r"C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning")
BIBLE_KEEPER = Path(r"C:\Users\Asus\AppData\Local\hermes\skills\project-management\project-bible-keeper\scripts\bible_keeper.py")
GANTT_SCRIPT = Path(r"C:\Users\Asus\Documents\Personal\Programs\DOSTorage\generate_gantt.py")
WATCH_PATTERNS = {"team_*.csv", "dostorage-v1-project-checklist.md"}
DEBOUNCE_SECONDS = 10
def _build_child_env() -> dict:
    """Forward required keeper env vars into the --tasks subprocess.

    On Windows, Task Scheduler / shell launches do not guarantee automatic
    propagation of session-specific vars, so we explicitly whitelist the
    Project Bible / Tasks / Meetings IDs.
    """
    env: dict = {}
    for key in [
        "PROJECT_BIBLE_DOC_ID",
        "PROJECT_TASKS_TAB_ID",
        "PROJECT_MEETINGS_TAB_ID",
    ]:
        value = os.environ.get(key)
        if value:
            env[key] = value
    return env

# Optional webhook configuration.
# Secrets MUST NOT be hardcoded. Provide them via environment variables:
# - WEBHOOK_BELL_TOKEN: Bell/DingTalk token
# - WEBHOOK_SLACK_URL: Slack webhook URL
# - WEBHOOK_DISCORD_URL: Discord webhook URL
# - WEBHOOK_CONFIG: Optional JSON file path with additional provider config.
# Example provider block:
# {
#   "providers": {
#     "bell": {"enabled": true, "token": "", "chatId": ""},
#     "slack": {"enabled": true, "webhook_url": ""},
#     "discord": {"enabled": true, "webhook_url": ""}
#   }
# }
WEBHOOK_CONFIG_PATH = os.environ.get("WEBHOOK_CONFIG") or Path(__file__).with_name("webhooks.json")
_loaded_webhook_config: dict | None = None


def _load_webhook_config() -> dict:
    global _loaded_webhook_config
    if _loaded_webhook_config is not None:
        return _loaded_webhook_config
    try:
        raw = Path(WEBHOOK_CONFIG_PATH).read_text(encoding="utf-8")
        _loaded_webhook_config = json.loads(raw)
    except Exception:
        _loaded_webhook_config = {}
    return _loaded_webhook_config


def _env_truthy(value: str | None) -> bool:
    return value and value.strip().lower() in {"1", "true", "yes", "y", "on"}


def _resolve_provider_config(name: str) -> tuple[bool, dict]:
    provider_block = _load_webhook_config().get("providers", {}).get(name, {})
    enabled = bool(provider_block.get("enabled")) or _env_truthy(os.environ.get(f"WEBHOOK_{name.upper()}_ENABLED"))
    return enabled, provider_block


def _post_json(url: str, payload: dict, timeout: int = 20) -> None:
    data = json.dumps(payload).encode("utf-8")
    req = urllib_request.Request(url, data=data, headers={"Content-Type": "application/json"})
    with urllib_request.urlopen(req, timeout=timeout) as resp:
        resp.read()


class TasksWatcherHandler(FileSystemEventHandler):
    def __init__(self):
        self._last_run = 0.0
        self._pending = False

    def _should_trigger(self, event: FileSystemEvent) -> bool:
        path = Path(event.src_path)
        if path.is_dir():
            return False
        name = path.name
        if name in {"team_fullstack.csv", "team_backend.csv", "team_frontend.csv", "team_pm.csv", "team_qa.csv", "dostorage-v1-project-checklist.md"}:
            return True
        if any(path.match(pattern) for pattern in [str(PLANNING_DIR / "team_*.csv")]):
            return True
        return False

    def _post_notifications(self) -> None:
        message = f"Tasks watcher completed successfully at {datetime.now(timezone.utc).isoformat()}"
        slack_url = os.environ.get("WEBHOOK_SLACK_URL")
        if slack_url:
            try:
                _post_json(slack_url, {"text": message})
            except Exception as exc:
                print(f"[{datetime.now().isoformat()}] Slack webhook failed: {exc}", file=sys.stderr, flush=True)

        discord_url = os.environ.get("WEBHOOK_DISCORD_URL")
        if discord_url:
            try:
                _post_json(discord_url, {"content": message})
            except Exception as exc:
                print(f"[{datetime.now().isoformat()}] Discord webhook failed: {exc}", file=sys.stderr, flush=True)

        bell_enabled, bell_cfg = _resolve_provider_config("bell")
        if bell_enabled:
            # Project-local Bell config precedence: JSON < env vars.
            token = bell_cfg.get("token") or os.environ.get("WEBHOOK_BELL_TOKEN")
            chat_id = bell_cfg.get("chatId") or os.environ.get("WEBHOOK_BELL_CHAT_ID")
            if token and chat_id:
                try:
                    payload = json.dumps({"msg": message}).encode("utf-8")
                    url = f"https://api.bell.iki.fi/send/{urllib_request.quote(str(token), safe='')}/message/{urllib_request.quote(str(chat_id), safe='')}"
                    req = urllib_request.Request(url, data=payload, headers={"Content-Type": "application/json"})
                    with urllib_request.urlopen(req, timeout=20) as resp:
                        resp.read()
                except Exception as exc:
                    print(f"[{datetime.now().isoformat()}] Bell webhook failed: {exc}", file=sys.stderr, flush=True)

    def _run_gantt(self) -> None:
        if not GANTT_SCRIPT.is_file():
            return
        print(f"[{datetime.now().isoformat()}] generate_gantt.py found. Starting Gantt generation...", flush=True)
        try:
            gantt = subprocess.run(
                [sys.executable, str(GANTT_SCRIPT)],
                cwd=str(PLANNING_DIR.parent),
                capture_output=True,
                text=True,
                check=False,
            )
            if gantt.returncode == 0:
                print(f"[{datetime.now().isoformat()}] Gantt OK: {gantt.stdout.strip()}", flush=True)
            else:
                print(f"[{datetime.now().isoformat()}] Gantt failed: {gantt.stderr.strip()}", file=sys.stderr, flush=True)
        except Exception as exc:
            print(f"[{datetime.now().isoformat()}] Gantt exception: {exc}", file=sys.stderr, flush=True)

    def _run(self) -> None:
        now = time.time()
        if now - self._last_run < DEBOUNCE_SECONDS and self._pending:
            self._pending = True
            return
        self._pending = False
        self._last_run = now
        print(f"[{datetime.now().isoformat()}] Detected planning change. Refreshing TASKS DETECTED...", flush=True)
        try:
            child_env = _build_child_env()
            completed = subprocess.run(
                [sys.executable, str(BIBLE_KEEPER), "--tasks"],
                cwd=str(PLANNING_DIR.parent),
                env=child_env or None,
                capture_output=True,
                text=True,
                check=False,
            )
            if completed.returncode == 0:
                print(f"[{datetime.now().isoformat()}] Refresh OK: {completed.stdout.strip()}", flush=True)
                try:
                    self._run_gantt()
                except Exception as exc:
                    print(f"[{datetime.now().isoformat()}] Gantt skipped: {exc}", file=sys.stderr, flush=True)
                try:
                    self._post_notifications()
                except Exception as exc:
                    print(f"[{datetime.now().isoformat()}] Notification skipped: {exc}", file=sys.stderr, flush=True)
            else:
                print(f"[{datetime.now().isoformat()}] Refresh failed: {completed.stderr.strip()}", file=sys.stderr, flush=True)
        except Exception as exc:
            print(f"[{datetime.now().isoformat()}] Refresh exception: {exc}", file=sys.stderr, flush=True)

    def on_modified(self, event: FileSystemEvent) -> None:
        if self._should_trigger(event):
            self._run()

    def on_created(self, event: FileSystemEvent) -> None:
        if self._should_trigger(event):
            self._run()

    def on_moved(self, event: FileSystemEvent) -> None:
        if self._should_trigger(event):
            self._run()


def main() -> None:
    handler = TasksWatcherHandler()
    observer = PollingObserver()
    observer.schedule(handler, str(PLANNING_DIR), recursive=False)
    observer.start()
    print(f"[{datetime.now().isoformat()}] TASKS DETECTED watcher started on {PLANNING_DIR}", flush=True)
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        observer.stop()
    observer.join()


if __name__ == "__main__":
    main()
