#!/usr/bin/env python3
import sys
import urllib.request


def main() -> int:
    for url in [
        "http://127.0.0.1:8080/health",
        "http://127.0.0.1:8080/metrics",
    ]:
        with urllib.request.urlopen(url, timeout=10) as resp:
            body = resp.read().decode("utf-8")
            print(f"GET {url} {resp.status}")
            print(body)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
