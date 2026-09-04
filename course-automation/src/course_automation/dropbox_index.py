from __future__ import annotations

import base64
import json
import re
import time
import urllib.parse
import urllib.request
from dataclasses import asdict, dataclass
from pathlib import Path


PREFETCH_RE = re.compile(r'registerStreamedPrefetch\("([^"]*)",\s*"([^"]*)"')
DROPBOX_FOLDER_RE = re.compile(r"https://www\.dropbox\.com/scl/fo/[A-Za-z0-9_./?=&%~-]+")


@dataclass(frozen=True)
class DropboxCategory:
    name: str
    slug: str
    expected_count: int | None
    url: str


def parse_dropbox_listing(listing_path: Path) -> list[DropboxCategory]:
    html = listing_path.read_text(encoding="utf-8", errors="ignore")
    decoded_chunks: list[str] = []
    for match in PREFETCH_RE.finditer(html):
        for value in match.groups():
            decoded = decode_base64_text(value)
            if decoded:
                decoded_chunks.append(decoded)

    seen_urls: set[str] = set()
    categories: dict[str, DropboxCategory] = {}
    for url_match in DROPBOX_FOLDER_RE.finditer("\n".join(decoded_chunks)):
        url = normalize_dropbox_url(url_match.group(0))
        if url in seen_urls:
            continue
        seen_urls.add(url)

        name = category_name_from_url(url)
        if not name:
            continue
        category = DropboxCategory(
            name=name,
            slug=slugify(name),
            expected_count=parse_expected_count(name),
            url=url,
        )
        categories[category.slug] = category

    return sorted(categories.values(), key=lambda item: item.name.upper())


def decode_base64_text(value: str) -> str:
    if not value:
        return ""
    padded = value + ("=" * ((4 - len(value) % 4) % 4))
    try:
        return base64.b64decode(padded).decode("utf-8", errors="ignore")
    except Exception:
        return ""


def normalize_dropbox_url(url: str) -> str:
    parsed = urllib.parse.urlparse(url)
    query = urllib.parse.parse_qs(parsed.query, keep_blank_values=True)
    allowed = {}
    for key in ("rlkey", "dl"):
        if key in query:
            allowed[key] = query[key][-1]
    normalized_query = urllib.parse.urlencode(allowed)
    return urllib.parse.urlunparse((parsed.scheme, parsed.netloc, parsed.path, "", normalized_query, ""))


def category_name_from_url(url: str) -> str:
    parsed = urllib.parse.urlparse(url)
    parts = [urllib.parse.unquote(part) for part in parsed.path.split("/") if part]
    if len(parts) < 5:
        return ""
    name = parts[-1].strip()
    if not name or name == "h" or name.startswith("NTL courses"):
        return ""
    return name


def parse_expected_count(name: str) -> int | None:
    match = re.search(r"\((\d+)\)\s*$", name)
    return int(match.group(1)) if match else None


def slugify(name: str) -> str:
    base = re.sub(r"\s*\(\d+\)\s*$", "", name).strip().lower()
    base = base.replace("&", "and")
    base = re.sub(r"[^a-z0-9]+", "-", base)
    return base.strip("-")


def as_download_url(url: str) -> str:
    parsed = urllib.parse.urlparse(url)
    query = urllib.parse.parse_qs(parsed.query, keep_blank_values=True)
    query["dl"] = ["1"]
    return urllib.parse.urlunparse(
        (parsed.scheme, parsed.netloc, parsed.path, "", urllib.parse.urlencode(query, doseq=True), "")
    )


def write_manifest(categories: list[DropboxCategory], output_path: Path) -> None:
    output_path.parent.mkdir(parents=True, exist_ok=True)
    payload = {
        "schema_version": "course_automation.dropbox_categories.v1",
        "generated_at": time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime()),
        "category_count": len(categories),
        "expected_course_total": sum(item.expected_count or 0 for item in categories),
        "categories": [asdict(item) for item in categories],
    }
    output_path.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")


def find_categories(categories: list[DropboxCategory], selectors: list[str]) -> list[DropboxCategory]:
    if not selectors:
        return categories

    normalized = {slugify(selector) for selector in selectors}
    found: list[DropboxCategory] = []
    for category in categories:
        if category.slug in normalized or slugify(category.name) in normalized:
            found.append(category)

    missing = sorted(normalized - {category.slug for category in found})
    if missing:
        raise ValueError(f"category not found: {', '.join(missing)}")
    return found


def download_category(category: DropboxCategory, output_dir: Path, skip_existing: bool = True) -> Path:
    output_dir.mkdir(parents=True, exist_ok=True)
    output_path = output_dir / f"{category.slug}.zip"
    if skip_existing and output_path.exists() and output_path.stat().st_size > 0:
        print(f"skip existing {category.name}: {output_path}")
        return output_path

    part_path = output_path.with_suffix(output_path.suffix + ".part")
    request = urllib.request.Request(
        as_download_url(category.url),
        headers={"User-Agent": "course-automation/1.0"},
    )
    print(f"downloading {category.name} -> {output_path}")
    with urllib.request.urlopen(request, timeout=120) as response:
        expected = int(response.headers.get("Content-Length") or 0)
        downloaded = 0
        last_report = 0.0
        with part_path.open("wb") as handle:
            while True:
                chunk = response.read(1024 * 1024)
                if not chunk:
                    break
                handle.write(chunk)
                downloaded += len(chunk)
                now = time.time()
                if now - last_report >= 10:
                    last_report = now
                    if expected:
                        percent = downloaded * 100 / expected
                        print(f"  {downloaded / 1024 / 1024:.1f} MB / {expected / 1024 / 1024:.1f} MB ({percent:.1f}%)")
                    else:
                        print(f"  {downloaded / 1024 / 1024:.1f} MB")
    part_path.replace(output_path)
    print(f"downloaded {category.name}: {output_path}")
    return output_path
