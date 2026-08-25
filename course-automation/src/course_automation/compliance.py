from __future__ import annotations

import csv
import json
import re
from datetime import datetime, timezone
from pathlib import Path
from typing import Iterable

from .docx_reader import parse_course_identity
from .settings import Settings
from .tracker import TRACKER_COLUMNS


SOURCE_EXTENSIONS = {".docx", ".txt", ".md", ".json", ".csv"}


def build_course_registry(source: Path, category: str, output_path: Path) -> list[dict[str, object]]:
    courses: list[dict[str, object]] = []
    for path in iter_source_files(source):
        unit_code, title = parse_source_identity(path)
        stat = path.stat()
        courses.append(
            {
                "course_id": unit_code,
                "unit_code": unit_code,
                "title": title,
                "category": category,
                "file_path": str(path.resolve()),
                "source_type": path.suffix.lower().lstrip("."),
                "source_bytes": stat.st_size,
                "source_modified": datetime.fromtimestamp(stat.st_mtime, tz=timezone.utc).isoformat(),
                "compliance_status": "not_checked",
                "last_checked": "",
            }
        )

    payload = {
        "schema_version": "course_automation.registry.v1",
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "source": str(source.resolve()),
        "category": category,
        "total_courses": len(courses),
        "courses": courses,
    }
    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")
    return courses


def scan_training_updates(
    registry_path: Path,
    updates_path: Path,
    output_path: Path,
    settings: Settings | None = None,
) -> dict[str, object]:
    registry_courses = load_registry_courses(registry_path)
    updates = load_training_update_rows(updates_path)
    update_lookup = {normalize_code(str(row.get("unit_code", ""))): row for row in updates if row.get("unit_code")}

    rows: list[dict[str, object]] = []
    for course in registry_courses:
        unit_code = normalize_code(str(course.get("unit_code") or course.get("course_id") or ""))
        update = update_lookup.get(unit_code)
        status = "up_to_date"
        action = "no_action"
        if update and bool(update.get("is_superseded")):
            status = "needs_compliance_review"
            action = "review_replacement_and_regenerate_course"
        elif update:
            status = "checked_current"

        rows.append(
            {
                "course_id": course.get("course_id", unit_code),
                "unit_code": unit_code,
                "title": course.get("title", ""),
                "category": course.get("category", ""),
                "file_path": course.get("file_path", ""),
                "compliance_status": status,
                "is_superseded": bool(update.get("is_superseded")) if update else False,
                "replacement_unit_code": update.get("replacement_unit_code", "") if update else "",
                "update_notes": update.get("update_notes", "") if update else "",
                "source_status": update.get("source_status", "") if update else "",
                "action": action,
            }
        )

    report = {
        "schema_version": "course_automation.compliance_report.v1",
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "registry": str(registry_path.resolve()),
        "updates_source": str(updates_path.resolve()),
        "total_courses": len(rows),
        "needs_review": sum(1 for row in rows if row["compliance_status"] == "needs_compliance_review"),
        "checked_current": sum(1 for row in rows if row["compliance_status"] == "checked_current"),
        "courses": rows,
    }
    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")

    if settings is not None:
        update_tracker_with_compliance(settings, rows)

    return report


def load_compliance_lookup(settings: Settings) -> dict[str, dict[str, object]]:
    if not settings.compliance_report_path.exists():
        return {}
    payload = json.loads(settings.compliance_report_path.read_text(encoding="utf-8"))
    courses = payload.get("courses", []) if isinstance(payload, dict) else []
    if not isinstance(courses, list):
        return {}
    return {
        normalize_code(str(row.get("course_id") or row.get("unit_code") or "")): row
        for row in courses
        if isinstance(row, dict)
    }


def apply_compliance_metadata(package: dict[str, object], compliance: dict[str, object] | None) -> None:
    if not compliance:
        package["compliance"] = {
            "status": "not_checked",
            "notice": "Compliance status has not yet been checked against an official update export.",
        }
        return

    package["compliance"] = {
        "status": compliance.get("compliance_status", "not_checked"),
        "is_superseded": bool(compliance.get("is_superseded")),
        "replacement_unit_code": compliance.get("replacement_unit_code", ""),
        "update_notes": compliance.get("update_notes", ""),
        "action": compliance.get("action", ""),
    }


def iter_source_files(source: Path) -> Iterable[Path]:
    return sorted(
        path
        for path in source.rglob("*")
        if path.is_file()
        and path.suffix.lower() in SOURCE_EXTENSIONS
        and not path.name.startswith("~$")
        and not path.name.endswith(".blueprint.json")
        and not path.name.endswith(".course.json")
    )


def parse_source_identity(path: Path) -> tuple[str, str]:
    if path.suffix.lower() == ".docx":
        return parse_course_identity(path)

    stem = path.stem
    match = re.match(r"(?P<id>[A-Za-z]{2,}\d+[A-Za-z0-9]*)[\s_-]*(?P<title>.*)", stem)
    if match:
        code = normalize_code(match.group("id"))
        title = match.group("title").replace("_", " ").replace("-", " ").strip()
        return code, title or code
    return normalize_code(stem), stem.replace("_", " ").replace("-", " ").strip() or stem


def load_registry_courses(registry_path: Path) -> list[dict[str, object]]:
    payload = json.loads(registry_path.read_text(encoding="utf-8"))
    if isinstance(payload, list):
        return [item for item in payload if isinstance(item, dict)]
    if isinstance(payload, dict) and isinstance(payload.get("courses"), list):
        return [item for item in payload["courses"] if isinstance(item, dict)]
    return []


def load_training_update_rows(path: Path) -> list[dict[str, object]]:
    if path.suffix.lower() == ".json":
        payload = json.loads(path.read_text(encoding="utf-8"))
        source_rows = payload.get("rows") or payload.get("courses") or payload if isinstance(payload, dict) else payload
        if isinstance(source_rows, list):
            return [normalize_update_row(row) for row in source_rows if isinstance(row, dict)]
        return []

    with path.open("r", encoding="utf-8-sig", newline="") as handle:
        reader = csv.DictReader(handle)
        return [normalize_update_row(row) for row in reader]


def normalize_update_row(row: dict[str, object]) -> dict[str, object]:
    unit_code = normalize_code(first_value(row, "unit_code", "Unit Code", "code", "Code", "National Code", "Training Component Code"))
    replacement = normalize_code(
        first_value(
            row,
            "replacement_unit_code",
            "Replacement Unit Code",
            "Replacement",
            "Superseded By",
            "SupersededBy",
        )
    )
    status = first_value(row, "status", "Status")
    current = first_value(row, "Current", "Is Current", "current_flag", "Current Flag")
    superseded_flag = first_value(row, "Superseded", "Is Superseded", "superseded")
    notes = first_value(
        row,
        "new_requirements",
        "New Requirements",
        "Change Summary",
        "Description",
        "Notes",
        "comments",
    )

    return {
        "unit_code": unit_code,
        "source_status": status or current or superseded_flag,
        "is_superseded": is_superseded_status(status, replacement, current, superseded_flag),
        "replacement_unit_code": replacement,
        "update_notes": notes,
        "raw": row,
    }


def first_value(row: dict[str, object], *keys: str) -> str:
    lowered = {key.lower().strip(): key for key in row}
    for key in keys:
        actual_key = lowered.get(key.lower().strip())
        if actual_key is None:
            continue
        value = row.get(actual_key)
        if value is not None and str(value).strip():
            return str(value).strip()
    return ""


def is_superseded_status(status: str, replacement: str, current: str = "", superseded_flag: str = "") -> bool:
    normalized = status.strip().lower()
    if replacement:
        return True
    flag = superseded_flag.strip().lower()
    if flag in {"true", "yes", "y", "1"}:
        return True
    if flag in {"false", "no", "n", "0"}:
        return False
    current_value = current.strip().lower()
    if current_value in {"true", "yes", "y", "1", "current"}:
        return False
    if current_value in {"false", "no", "n", "0", "not current", "non-current"}:
        return True
    if normalized in {"false", "no", "n", "0", "current"}:
        return False
    return any(token in normalized for token in ("superseded", "deleted", "not current", "non-current", "expired"))


def update_tracker_with_compliance(settings: Settings, report_rows: list[dict[str, object]]) -> None:
    if not settings.tracker_path.exists():
        return

    report_by_course = {
        normalize_code(str(row.get("course_id") or row.get("unit_code") or "")): row
        for row in report_rows
    }
    with settings.tracker_path.open("r", encoding="utf-8-sig", newline="") as handle:
        tracker_rows = list(csv.DictReader(handle))

    for row in tracker_rows:
        course_id = normalize_code(row.get("Course ID", ""))
        report = report_by_course.get(course_id)
        if not report:
            continue
        if report.get("compliance_status") == "needs_compliance_review":
            row["Content Updated"] = "needs_compliance_review"
            row["QA Status"] = "blocked_compliance_review"
            notes = str(report.get("update_notes") or "Unit is superseded or non-current in update export.")
            replacement = str(report.get("replacement_unit_code") or "")
            row["Error/Notes"] = f"{notes} Replacement: {replacement}".strip()
        elif row.get("QA Status") in {"", "pending"}:
            row["Error/Notes"] = "Compliance update export checked; no review flag found."

    with settings.tracker_path.open("w", encoding="utf-8-sig", newline="") as handle:
        writer = csv.DictWriter(handle, fieldnames=TRACKER_COLUMNS)
        writer.writeheader()
        writer.writerows(tracker_rows)


def normalize_code(value: str) -> str:
    return re.sub(r"[^A-Za-z0-9]", "", value).upper()
