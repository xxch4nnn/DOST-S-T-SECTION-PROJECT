import json
from pathlib import Path

HERMES_HOME = Path.home() / "AppData" / "Local" / "hermes"
token_path = HERMES_HOME / "google_token.json"
raw = json.loads(token_path.read_text(encoding="utf-8"))

allowed = {
    "https://www.googleapis.com/auth/documents",
    "https://www.googleapis.com/auth/drive",
    "https://www.googleapis.com/auth/drive.file",
    "https://www.googleapis.com/auth/drive.readonly",
    "https://www.googleapis.com/auth/spreadsheets",
    "https://www.googleapis.com/auth/spreadsheets.readonly",
    "https://www.googleapis.com/auth/script.projects",
    "https://www.googleapis.com/auth/script.deployments",
    "https://www.googleapis.com/auth/cloud-platform",
    "openid",
    "email",
    "profile",
}

current = set(raw.get("scopes", []))
print("CURRENT_SCOPES:")
for s in sorted(current):
    mark = "OK" if s in allowed else "NOT_IN_ALLOWED"
    print(mark, s)

print("REMOVE:", sorted(current - allowed))
print("KEEP:", sorted(current & allowed))
