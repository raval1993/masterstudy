from __future__ import annotations

import csv
import json
from dataclasses import asdict
from datetime import datetime, timezone
from pathlib import Path

from .docx_reader import ExtractedCourse


TRACKER_COLUMNS = [
    "Course ID",
    "Course Name",
    "Category",
    "Source File",
    "Source Status",
    "Extracted",
    "Word Count",
    "Section Count",
    "Content Updated",
    "Script Generated",
    "Audio Generated",
    "Video Generated",
    "Quiz Added",
    "MasterStudy Added",
    "Coursebox Status",
    "QA Status",
    "Live URL",
    "Date Completed",
    "Error/Notes",
]


def ensure_output_dirs(*paths: Path) -> None:
    for path in paths:
        path.mkdir(parents=True, exist_ok=True)


def course_to_record(course: ExtractedCourse, source_path: Path) -> dict[str, object]:
    source = source_path.resolve()
    stat = source.stat()
    extracted_at = datetime.now(timezone.utc).isoformat()
    return {
        "course_id": course.course_id,
        "title": course.title,
        "category": course.category,
        "source_file": str(source),
        "source_bytes": stat.st_size,
        "source_modified": datetime.fromtimestamp(stat.st_mtime, tz=timezone.utc).isoformat(),
        "extracted_at": extracted_at,
        "word_count": course.word_count,
        "paragraph_count": course.paragraph_count,
        "table_count": course.table_count,
        "section_count": len(course.sections),
        "sections": [
            {
                "heading": section.heading,
                "level": section.level,
                "word_count": section.word_count,
                "body": section.body,
            }
            for section in course.sections
        ],
        "full_text": course.full_text,
    }


def write_course_json(record: dict[str, object], output_path: Path) -> None:
    output_path.write_text(json.dumps(record, ensure_ascii=False, indent=2), encoding="utf-8")


def write_course_markdown(record: dict[str, object], output_path: Path) -> None:
    lines = [
        f"# {record['course_id']} - {record['title']}",
        "",
        f"Category: {record['category']}",
        f"Source: {record['source_file']}",
        f"Word count: {record['word_count']}",
        "",
    ]
    for section in record["sections"]:
        level = min(int(section["level"]) + 1, 6)
        lines.append("#" * level + " " + str(section["heading"]))
        lines.append("")
        body = section.get("body", [])
        if body:
            lines.append("\n\n".join(str(item) for item in body))
            lines.append("")
    output_path.write_text("\n".join(lines).strip() + "\n", encoding="utf-8")


def write_blueprint(blueprint: dict[str, object], output_path: Path) -> None:
    output_path.write_text(json.dumps(blueprint, ensure_ascii=False, indent=2), encoding="utf-8")


def build_tracker_rows(records: list[dict[str, object]]) -> list[dict[str, str]]:
    rows: list[dict[str, str]] = []
    for record in sorted(records, key=lambda item: str(item["course_id"])):
        rows.append(
            {
                "Course ID": str(record["course_id"]),
                "Course Name": str(record["title"]),
                "Category": str(record["category"]),
                "Source File": str(record["source_file"]),
                "Source Status": "source_found",
                "Extracted": "yes",
                "Word Count": str(record["word_count"]),
                "Section Count": str(record["section_count"]),
                "Content Updated": "source_structured",
                "Script Generated": "pending",
                "Audio Generated": "pending",
                "Video Generated": "pending",
                "Quiz Added": "pending",
                "MasterStudy Added": "pending",
                "Coursebox Status": "",
                "QA Status": "pending",
                "Live URL": "",
                "Date Completed": "",
                "Error/Notes": "",
            }
        )
    return rows


def write_tracker(rows: list[dict[str, str]], tracker_path: Path) -> None:
    with tracker_path.open("w", newline="", encoding="utf-8-sig") as handle:
        writer = csv.DictWriter(handle, fieldnames=TRACKER_COLUMNS)
        writer.writeheader()
        writer.writerows(rows)


def write_summary(records: list[dict[str, object]], summary_path: Path) -> None:
    summary = {
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "total_courses": len(records),
        "total_words": sum(int(record["word_count"]) for record in records),
        "total_sections": sum(int(record["section_count"]) for record in records),
        "courses": [
            {
                "course_id": record["course_id"],
                "title": record["title"],
                "category": record["category"],
                "word_count": record["word_count"],
                "section_count": record["section_count"],
                "table_count": record["table_count"],
            }
            for record in sorted(records, key=lambda item: str(item["course_id"]))
        ],
    }
    summary_path.write_text(json.dumps(summary, ensure_ascii=False, indent=2), encoding="utf-8")
