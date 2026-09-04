from __future__ import annotations

import json
import re
import shutil
import time
from pathlib import Path
from zipfile import ZIP_DEFLATED, ZipFile

from .settings import Settings


def export_course_batch(
    settings: Settings,
    output_root: Path,
    batch_name: str,
    category_filters: list[str] | None = None,
    course_ids: list[str] | None = None,
    create_zip: bool = True,
) -> dict[str, object]:
    packages = select_packages(settings, category_filters or [], course_ids or [])
    if not packages:
        raise ValueError("No generated course packages matched the requested batch filters.")

    slug = slugify(batch_name)
    batch_root = output_root / slug
    reset_batch_root(output_root, batch_root)
    content_root = batch_root / "course-automation"
    courses_dir = content_root / "courses"
    blueprints_dir = content_root / "blueprints"
    media_dir = content_root / "media"
    videos_dir = content_root / "videos"
    for directory in (courses_dir, blueprints_dir, media_dir, videos_dir):
        directory.mkdir(parents=True, exist_ok=True)

    copied = {"courses": 0, "blueprints": 0, "media": 0, "videos": 0}
    manifest_courses = []

    for package_path, package in packages:
        course_id = str(package.get("course_id", "")).strip()
        shutil.copy2(package_path, courses_dir / package_path.name)
        copied["courses"] += 1

        blueprint_path = settings.blueprints_dir / f"{course_id}.blueprint.json"
        if blueprint_path.exists():
            shutil.copy2(blueprint_path, blueprints_dir / blueprint_path.name)
            copied["blueprints"] += 1

        copied["media"] += copy_package_assets(package, media_dir)
        copied["videos"] += copy_package_videos(package, videos_dir)
        manifest_courses.append(
            {
                "course_id": course_id,
                "title": package.get("title", ""),
                "category": package.get("category", ""),
                "lesson_count": package.get("lesson_count", 0),
                "video_status": package.get("video_status", ""),
            }
        )

    manifest = {
        "schema_version": "course_automation.transfer_batch.v1",
        "generated_at": time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime()),
        "batch_name": batch_name,
        "batch_slug": slug,
        "course_count": len(packages),
        "copied": copied,
        "extract_under": "wp-content",
        "server_target": "public_html/masterstudy/wp-content/course-automation",
        "courses": sorted(manifest_courses, key=lambda item: str(item["course_id"])),
    }
    manifest_path = batch_root / "batch-manifest.json"
    manifest_path.write_text(json.dumps(manifest, ensure_ascii=False, indent=2), encoding="utf-8")

    zip_path = None
    if create_zip:
        zip_path = output_root / f"{slug}.zip"
        write_zip(batch_root, zip_path)
        manifest["zip_path"] = str(zip_path.resolve())
        manifest_path.write_text(json.dumps(manifest, ensure_ascii=False, indent=2), encoding="utf-8")

    manifest["batch_root"] = str(batch_root.resolve())
    return manifest


def select_packages(settings: Settings, category_filters: list[str], course_ids: list[str]) -> list[tuple[Path, dict[str, object]]]:
    selected_categories = {normalize_filter(value) for value in category_filters if value.strip()}
    selected_ids = {value.upper() for value in course_ids if value.strip()}
    packages = []

    for package_path in sorted(settings.generated_courses_dir.glob("*.course.json")):
        package = json.loads(package_path.read_text(encoding="utf-8"))
        course_id = str(package.get("course_id", "")).upper()
        category = normalize_filter(str(package.get("category", "")))
        if selected_ids and course_id not in selected_ids:
            continue
        if selected_categories and category not in selected_categories:
            continue
        packages.append((package_path, package))

    return packages


def copy_package_assets(package: dict[str, object], media_dir: Path) -> int:
    copied = 0
    seen: set[str] = set()
    for asset in iter_assets(package):
        if str(asset.get("type", "")) != "image":
            continue
        relative_path = clean_relative_path(str(asset.get("relative_path", "")))
        source_path = Path(str(asset.get("source_path", "")))
        if not relative_path or not source_path.exists() or relative_path in seen:
            continue
        target = media_dir / relative_path
        target.parent.mkdir(parents=True, exist_ok=True)
        shutil.copy2(source_path, target)
        seen.add(relative_path)
        copied += 1
    return copied


def copy_package_videos(package: dict[str, object], videos_dir: Path) -> int:
    copied = 0
    seen: set[str] = set()
    for video in iter_videos(package):
        relative_path = clean_relative_path(str(video.get("relative_path", "")))
        source_path = Path(str(video.get("source_path", "")))
        if not relative_path or not source_path.exists() or relative_path in seen:
            continue
        target = videos_dir / relative_path
        target.parent.mkdir(parents=True, exist_ok=True)
        shutil.copy2(source_path, target)
        seen.add(relative_path)
        copied += 1
    return copied


def iter_assets(package: dict[str, object]):
    for module in package.get("modules", []):
        if not isinstance(module, dict):
            continue
        for lesson in module.get("lessons", []):
            if not isinstance(lesson, dict):
                continue
            for asset in lesson.get("assets", []):
                if isinstance(asset, dict):
                    yield asset


def iter_videos(package: dict[str, object]):
    course_video = package.get("course_video")
    if isinstance(course_video, dict):
        yield course_video
    for module in package.get("modules", []):
        if not isinstance(module, dict):
            continue
        for lesson in module.get("lessons", []):
            if not isinstance(lesson, dict):
                continue
            video = lesson.get("video")
            if isinstance(video, dict):
                yield video


def write_zip(source_root: Path, zip_path: Path) -> None:
    zip_path.parent.mkdir(parents=True, exist_ok=True)
    with ZipFile(zip_path, "w", compression=ZIP_DEFLATED, compresslevel=6) as archive:
        for path in sorted(item for item in source_root.rglob("*") if item.is_file()):
            archive.write(path, path.relative_to(source_root).as_posix())


def reset_batch_root(output_root: Path, batch_root: Path) -> None:
    output_root = output_root.resolve()
    batch_root = batch_root.resolve()
    if batch_root == output_root or output_root not in batch_root.parents:
        raise ValueError(f"Refusing to clear unsafe batch folder: {batch_root}")
    if batch_root.exists():
        shutil.rmtree(batch_root)


def clean_relative_path(value: str) -> str:
    value = value.strip().replace("\\", "/").lstrip("/")
    if ".." in value:
        return ""
    return value


def normalize_filter(value: str) -> str:
    return re.sub(r"[^a-z0-9]+", "-", value.lower()).strip("-")


def slugify(value: str) -> str:
    slug = normalize_filter(value)
    return slug or "course-batch"
