import os
import json
from pathlib import Path

HERMES_HOME = Path.home() / "AppData" / "Local" / "hermes"
DEFAULT_BIBLE_ID = "1TL6YADi71bi9fHAaF8YAypZWW-jCpDGkQvJosera-Ms"
BIBLE_ID = os.getenv("PROJECT_BIBLE_DOC_ID", DEFAULT_BIBLE_ID)
token_path = HERMES_HOME / "google_token.json"

from google.oauth2.credentials import Credentials
from google.auth.transport.requests import Request
from googleapiclient.discovery import build

creds = Credentials.from_authorized_user_file(str(token_path))
if creds.expired and creds.refresh_token:
    creds.refresh(Request())

docs = build("docs", "v1", credentials=creds)
doc = docs.documents().get(documentId=BIBLE_ID, includeTabsContent=True).execute()

print("DOC_TITLE:", doc.get("title"))
print("BIBLE_ID:", BIBLE_ID)

print("\n=== TOP LEVEL TABS ===")
for idx, tab in enumerate(doc.get("tabs", [])):
    props = tab.get("tabProperties", {})
    print(idx, "tabId=", props.get("tabId"), "title=", props.get("title"), "index=", props.get("index"))

print("\n=== ROOT BODY CONTENT (Bible Center) ===")
roots = doc.get("body", {}).get("content", []) or []
for idx, elem in enumerate(roots[:80]):
    text = "".join(
        run.get("textRun", {}).get("content", "")
        for run in elem.get("paragraph", {}).get("elements", [])
        if "textRun" in run
    ).replace("\n", "⏎")
    print(f"[root {idx}] startIndex={elem.get('startIndex')} endIndex={elem.get('endIndex')} text={text[:180]!r}")

print("\n=== TASKS DETECTED TAB (if present) ===")
for tab in doc.get("tabs", []):
    props = tab.get("tabProperties", {})
    title = (props.get("title") or "").strip().upper()
    if title == "TASKS DETECTED":
        content = tab.get("documentTab", {}).get("body", {}).get("content", []) or []
        print("tabId:", props.get("tabId"))
        for idx, elem in enumerate(content[:40]):
            text = "".join(
                run.get("textRun", {}).get("content", "")
                for run in elem.get("paragraph", {}).get("elements", [])
                if "textRun" in run
            ).replace("\n", "⏎")
            print(f"[task tab {idx}] startIndex={elem.get('startIndex')} endIndex={elem.get('endIndex')} text={text[:180]!r}")
        break
else:
    print("No TASKS DETECTED tab found")

print("\n=== EXPECTED MARKERS ===")
print(repr("<!-- BIBLE_KEEPER_TASKS_START -->"))
print(repr("<!-- BIBLE_KEEPER_TASKS_END -->"))
