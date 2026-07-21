import os
import json
from pathlib import Path

HERMES_HOME = Path.home() / "AppData" / "Local" / "hermes"
token_path = HERMES_HOME / "google_token.json"

raw = json.loads(token_path.read_text(encoding="utf-8"))
print("TOKEN_FILE:", token_path)
print("TYPE:", raw.get("type", "authorized_user"))
print("HAS_REFRESH_TOKEN:", bool(raw.get("refresh_token")))
print("EXPIRY:", raw.get("expiry"))
print("SCOPES:", raw.get("scopes"))
print("RAW_KEYS:", sorted(raw.keys()))
print("VALID:", raw.get("valid"))

from google.oauth2.credentials import Credentials
from google.auth.transport.requests import Request
creds = Credentials.from_authorized_user_file(str(token_path))
print("CRED_VALID:", creds.valid)
print("CRED_EXPIRED:", creds.expired)
print("CRED_REFRESH_TOKEN:", bool(creds.refresh_token))

if creds.expired and creds.refresh_token:
    try:
        creds.refresh(Request())
        print("REFRESH_OK:", creds.valid)
    except Exception as e:
        print("REFRESH_FAILED:", repr(e))
else:
    print("REFRESH_NOT_NEEDED")

print("TOKEN:")
print(creds.to_json())
