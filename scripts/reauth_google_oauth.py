#!/usr/bin/env python3
"""Re-auth Google OAuth token with Apps Script + Drive scopes."""
import sys
from pathlib import Path
from google.oauth2.credentials import Credentials
from google.auth.transport.requests import Request
from google_auth_oauthlib.flow import InstalledAppFlow

SECRET = Path.home() / 'AppData/Local/hermes/google_client_secret.json'
TOKEN = Path.home() / 'AppData/Local/hermes/google_token.json'
SCOPES = [
    # Existing Docs scope
    'https://www.googleapis.com/auth/documents',
    # Drive needed for Apps Script
    'https://www.googleapis.com/auth/drive',
    # Apps Script
    'https://www.googleapis.com/auth/script.projects',
    'https://www.googleapis.com/auth/script.deployments',
    # Catch-all cloud platform
    'https://www.googleapis.com/auth/cloud-platform',
]


def main():
    creds = None
    if TOKEN.exists():
        try:
            creds = Credentials.from_authorized_user_file(str(TOKEN), SCOPES)
        except Exception:
            creds = None

    if creds and creds.expired and creds.refresh_token:
        try:
            creds.refresh(Request())
            print('Token refreshed OK')
        except Exception as exc:
            print(f'Refresh failed: {exc}; re-authorizing', file=sys.stderr)
            creds = None

    if not creds or not creds.valid:
        print('Opening browser for Google OAuth consent...')
        flow = InstalledAppFlow.from_client_secrets_file(str(SECRET), SCOPES)
        creds = flow.run_local_server(port=0)
        print('Authorization received')

    TOKEN.write_text(creds.to_json(), encoding='utf-8')
    print(f'Token saved to {TOKEN}')
    print(f'Token has scopes: {creds.scopes}')
    missing = [s for s in SCOPES if s not in (creds.scopes or [])]
    if missing:
        print(f'WARNING: missing scopes: {missing}', file=sys.stderr)
        sys.exit(3)
    print('All scopes present')


if __name__ == '__main__':
    main()
