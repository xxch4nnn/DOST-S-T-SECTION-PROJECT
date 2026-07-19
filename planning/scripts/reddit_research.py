#!/usr/bin/env python3
"""
Internal Reddit research helper.

Policy:
- Only uses Python stdlib + project-local data.
- No blocked npm/pip packages.
- Public read-only interfaces only.
- Outputs are JSON/Markdown files for audit.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import os
import random
import re
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from collections import Counter
from datetime import datetime, timezone
from pathlib import Path
from typing import Any

DEFAULT_OUT = Path(
    r"C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning\output\reddit"
)
DEFAULT_USER_AGENT = "HermesRedditResearch/0.1 (DOSTorage; internal research)"
REQUEST_TIMEOUT = int(os.getenv("REDDIT_REQUEST_TIMEOUT", "20"))
RETRY_BACKOFF = [1, 2, 4]


def sanitize_name(value: str) -> str:
    return re.sub(r"[^a-zA-Z0-9-_=]", "_", value)[:100].strip("_") or "unnamed"


def out_path(name: str, suffix: str = "json") -> Path:
    today = datetime.now().strftime("%Y-%m-%d")
    base = DEFAULT_OUT / today
    base.mkdir(parents=True, exist_ok=True)
    safe = sanitize_name(name)
    return base / f"{safe}.{suffix}"


def request_json(url: str) -> Any:
    req = urllib.request.Request(url, headers={"User-Agent": DEFAULT_USER_AGENT})
    for attempt, backoff in enumerate(RETRY_BACKOFF, start=1):
        try:
            with urllib.request.urlopen(req, timeout=REQUEST_TIMEOUT) as resp:
                charset = resp.headers.get_content_charset() or "utf-8"
                text = resp.read().decode(charset, errors="replace")
                try:
                    return json.loads(text)
                except json.JSONDecodeError:
                    return {
                        "error": "invalid_json",
                        "url": url,
                        "preview": text[:200],
                    }
        except (urllib.error.URLError, TimeoutError, OSError) as exc:
            if attempt == len(RETRY_BACKOFF):
                return {
                    "error": "request_failed",
                    "url": url,
                    "attempts": attempt,
                    "message": str(exc),
                }
            time.sleep(backoff + random.uniform(0, 0.25))


def reddit_public_json(path: str) -> Any:
    url = f"https://www.reddit.com{path}"
    if ".json" not in path:
        if "?" in path:
            url += ".json"
        else:
            url += ".json"
    return request_json(url)


def extract_post_kind(data: Any) -> str | None:
    if isinstance(data, dict):
        if data.get("kind") == "t3" and isinstance(data.get("data"), dict):
            post = data["data"]
            name = post.get("name", "")
            title = post.get("title", "")
            text = post.get("selftext", "")
            if title:
                return title if len(title) < 180 else title[:177] + "..."
            if name:
                return name
            if text:
                return text[:120]
    return None


def summarize_text(text: str, max_sentences: int = 5) -> str:
    sentences = re.split(r"(?<=[.!?])\s+", text.strip())
    if not sentences:
        return text[:240]
    if len(sentences) <= max_sentences:
        return " ".join(sentences)
    return " ".join(sentences[:max_sentences])[:1200]


def run_discover(query: str, limit: int = 15):
    parsed = urllib.parse.quote_plus(query)
    path = f"/search.json?q={parsed}&limit={limit}&sort=relevance&type=subreddit"
    data = reddit_public_json(path)
    output: dict[str, Any] = {
        "query": query,
        "source_url": f"https://www.reddit.com/search?q={parsed}&type=subreddit",
        "timestamps": {
            "started": datetime.now(timezone.utc).isoformat(),
            "finished": datetime.now(timezone.utc).isoformat(),
        },
        "subreddits": [],
        "errors": [],
        "notes": [
            "This is a public, read-only search.",
            "If this run fails with request_failed, retry later or check network.",
        ],
    }

    sub_names: list[str] = []
    if isinstance(data, dict):
        if data.get("error"):
            output["errors"].append(data)
        else:
            for child in data.get("data", {}).get("children", []):
                kind = child.get("kind")
                sub_data = child.get("data", {})
                if kind == "t5":
                    name = sub_data.get("display_name") or sub_data.get("name")
                    if name and name not in sub_names:
                        sub_names.append(name)
                    output["subreddits"].append(
                        {
                            "name": name,
                            "title": sub_data.get("title"),
                            "subscribers": sub_data.get("subscribers"),
                            "description": sub_data.get("public_description"),
                            "url": f"https://www.reddit.com/r/{name}",
                        }
                    )

    output["subreddits"] = output["subreddits"][:limit]
    output["count"] = len(output["subreddits"])
    path_out = out_path(f"discover_{query}")
    path_out.write_text(json.dumps(output, indent=2), encoding="utf-8")
    print(path_out)


def run_collect(subreddit: str, limit: int = 20, sort: str = "new"):
    subreddit = subreddit.strip().lstrip("/r/")
    path = f"/r/{urllib.parse.quote(subreddit)}/{sort}.json?limit={min(limit, 100)}"
    data = reddit_public_json(path)
    output: dict[str, Any] = {
        "subreddit": subreddit,
        "limit": limit,
        "sort": sort,
        "source_url": f"https://www.reddit.com/r/{subreddit}/{sort}",
        "timestamps": {
            "started": datetime.now(timezone.utc).isoformat(),
            "finished": datetime.now(timezone.utc).isoformat(),
        },
        "posts": [],
        "summary": {},
        "errors": [],
    }

    if isinstance(data, dict):
        if data.get("error"):
            output["errors"].append(data)
        else:
            children = data.get("data", {}).get("children", [])
            items: list[dict[str, Any]] = []
            for child in children:
                kind = child.get("kind")
                post = child.get("data", {})
                if kind != "t3":
                    continue
                title = post.get("title") or ""
                selftext = post.get("selftext") or ""
                comments = post.get("num_comments") or 0
                score = post.get("score") or 0
                flair = post.get("link_flair_text") or post.get("link_flair_template_id")
                items.append(
                    {
                        "id": post.get("id"),
                        "name": post.get("name"),
                        "title": title,
                        "author": post.get("author"),
                        "created_utc": post.get("created_utc"),
                        "score": score,
                        "num_comments": comments,
                        "flair": flair,
                        "permalink": f"https://www.reddit.com{post.get('permalink', '')}",
                        "selftext_summary": summarize_text(selftext, max_sentences=3),
                        "selftext_length": len(selftext),
                        "is_self": post.get("is_self"),
                        "domain": post.get("domain"),
                        "url": post.get("url"),
                    }
                )
                if len(items) >= limit:
                    break
            output["posts"] = items
            output["summary"] = {
                "total_items": len(items),
                "avg_score": round(sum(i["score"] for i in items) / len(items), 2) if items else 0,
                "avg_comments": round(sum(i["num_comments"] for i in items) / len(items), 2) if items else 0,
                "flairs": sorted(
                    Counter(i["flair"] for i in items if i.get("flair")).items(),
                    key=lambda x: x[1],
                    reverse=True,
                )[:10],
                "domain_top": sorted(
                    Counter(i.get("domain") for i in items if i.get("domain")).items(),
                    key=lambda x: x[1],
                    reverse=True,
                )[:10],
            }
    else:
        output["errors"].append({"error": "unexpected_response", "type": type(data).__name__})

    path_out = out_path(f"collect_{subreddit}")
    path_out.write_text(json.dumps(output, indent=2), encoding="utf-8")
    print(path_out)


def run_summarize(path_str: str):
    p = Path(path_str)
    if not p.exists():
        raise FileNotFoundError(p)
    data = json.loads(p.read_text(encoding="utf-8"))
    summary: dict[str, Any] = {
        "source": str(p),
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "items": 0,
        "highlights": [],
        "error": None,
    }

    subreddits = data.get("subreddits") or []
    posts = data.get("posts") or []
    summary["items"] = max(len(subreddits), len(posts))

    if subreddits:
        try:
            sorted_subs = sorted(subreddits, key=lambda x: (x.get("subscribers") or 0), reverse=True)
            summary["highlights"] = [
                f"Top subreddit: r/{sorted_subs[0].get('name')} ({sorted_subs[0].get('subscribers')} subscribers)"
            ]
        except Exception:
            pass
        summary["top_subreddits"] = [s.get("name") for s in subreddits[:8] if s.get("name")]

    if posts:
        try:
            by_score = sorted(posts, key=lambda x: x.get("score") or 0, reverse=True)
            by_comments = sorted(posts, key=lambda x: x.get("num_comments") or 0, reverse=True)
            summary["highlights"].extend(
                [
                    f"Top by score: {by_score[0].get('title')!r} ({by_score[0].get('score')} score)",
                    f"Most discussed: {by_comments[0].get('title')!r} ({by_comments[0].get('num_comments')} comments)",
                ]
            )
            summary["stats"] = data.get("summary", {})
        except Exception:
            pass
        summary["top_posts"] = [
            {"title": post.get("title"), "score": post.get("score"), "comments": post.get("num_comments")}
            for post in posts[:12]
        ]

    if not summary["highlights"]:
        summary["error"] = "no_content_to_summarize"

    path_out = out_path("summary")
    path_out.write_text(json.dumps(summary, indent=2), encoding="utf-8")
    print(path_out)


def main():
    parser = argparse.ArgumentParser(description="Internal Reddit research helper")
    parser.add_argument("--timeout", type=int, default=REQUEST_TIMEOUT, help="HTTP timeout seconds")
    parser.add_argument("--output-dir", default=str(DEFAULT_OUT), help="Override output root")
    sub = parser.add_subparsers(dest="command", required=True)

    p1 = sub.add_parser("discover", help="Discover candidate target subreddits")
    p1.add_argument("query")
    p1.add_argument("--limit", type=int, default=15)
    p1.set_defaults(func=lambda a: run_discover(a.query, a.limit))

    p2 = sub.add_parser("collect", help="Collect thread/post data from a subreddit")
    p2.add_argument("subreddit")
    p2.add_argument("--limit", type=int, default=20)
    p2.add_argument("--sort", default="new", choices=["new", "hot", "top", "rising", "controversial"])
    p2.set_defaults(func=lambda a: run_collect(a.subreddit, a.limit, a.sort))

    p3 = sub.add_parser("summarize", help="Summarize collected output")
    p3.add_argument("path")
    p3.set_defaults(func=lambda a: run_summarize(a.path))

    args = parser.parse_args()
    globals()["REQUEST_TIMEOUT"] = getattr(args, "timeout", REQUEST_TIMEOUT)
    globals()["DEFAULT_OUT"] = Path(getattr(args, "output_dir", str(DEFAULT_OUT)))
    args.func(args)


if __name__ == "__main__":
    main()
