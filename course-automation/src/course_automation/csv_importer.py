from __future__ import annotations

import csv
import json
import re
import zipfile
from collections import Counter
from dataclasses import dataclass, field
from datetime import datetime, timezone
from pathlib import Path
from urllib.parse import unquote, urlparse

from .tracker import TRACKER_COLUMNS


URL_RE = re.compile(r"https?://[^\s<>\"']+", re.IGNORECASE)
WORD_RE = re.compile(r"\b[\w'-]+\b")
COURSE_ID_RE = re.compile(r"^([A-Za-z]{2,}[A-Za-z0-9]*\d[A-Za-z0-9]*)(?=\s*[-:])")
PLACEHOLDER_RE = re.compile(r"\b(coming soon|not available|course content)\b", re.IGNORECASE)
GENERIC_LESSON_TITLES = {
    "copy and paste the link in your browser",
    "lesson title",
    "source material",
    "",
}
DOCUMENT_CATEGORY_VALUES = {
    "audit mapping",
    "audit matrix",
    "auditors handbook",
    "course book",
    "delivery manual",
    "exam outline",
    "mapping doc",
    "module 1",
    "pmbok-guide",
    "risk control",
    "test paper",
}


@dataclass
class CsvSourceDocument:
    url: str
    filename: str
    title: str
    external_file: str = ""
    row_category: str = ""
    course_content: str = ""
    lesson_title: str = ""
    lesson_content: str = ""


@dataclass
class CsvCourseSeed:
    course_id: str
    title: str
    category: str
    source_file: str
    row_count: int = 0
    course_status: str = ""
    level: str = ""
    price: str = ""
    sale_price: str = ""
    one_time_purchase: str = ""
    not_in_membership: str = ""
    course_views: str = ""
    current_students: str = ""
    duration_info: str = ""
    video_duration: str = ""
    excerpt: str = ""
    announcement: str = ""
    course_content: str = ""
    faqs: list[dict[str, str]] = field(default_factory=list)
    external_files: Counter[str] = field(default_factory=Counter)
    source_documents: list[CsvSourceDocument] = field(default_factory=list)
    _source_urls: set[str] = field(default_factory=set)


def generate_courses_from_csv(
    source: Path,
    output_root: Path,
    limit: int = 0,
    max_lessons_per_course: int = 0,
) -> dict[str, object]:
    output_root.mkdir(parents=True, exist_ok=True)
    courses_dir = output_root / "generated" / "courses"
    courses_dir.mkdir(parents=True, exist_ok=True)

    seeds = load_csv_course_seeds(source)
    if limit > 0:
        seeds = seeds[:limit]

    written: list[Path] = []
    source_rows: list[dict[str, str]] = []
    tracker_rows: list[dict[str, str]] = []
    total_lessons = 0
    total_source_documents = 0

    for seed in seeds:
        package = build_course_package(seed, max_lessons_per_course=max_lessons_per_course)
        output_path = courses_dir / f"{seed.course_id}.course.json"
        output_path.write_text(json.dumps(package, ensure_ascii=False, indent=2), encoding="utf-8")
        written.append(output_path)
        lesson_count = int(package.get("lesson_count") or 0)
        total_lessons += lesson_count
        total_source_documents += len(seed.source_documents)
        tracker_rows.append(build_tracker_row(seed, package))

        for document in seed.source_documents:
            source_rows.append(
                {
                    "Course ID": seed.course_id,
                    "Course Title": seed.title,
                    "Category": seed.category,
                    "Document Title": document.title,
                    "Document File": document.filename,
                    "Document URL": document.url,
                    "External File": document.external_file,
                    "Row Category": document.row_category,
                }
            )

    write_tracker(tracker_rows, output_root / "course_tracker.csv")
    write_source_documents(source_rows, output_root / "source_documents.csv")
    summary = {
        "schema_version": "course_automation.csv_generation_summary.v1",
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "source": str(source.resolve()),
        "output_root": str(output_root.resolve()),
        "course_count": len(written),
        "lesson_count": total_lessons,
        "source_document_references": total_source_documents,
        "course_json_dir": str(courses_dir.resolve()),
        "source_documents_csv": str((output_root / "source_documents.csv").resolve()),
        "tracker_csv": str((output_root / "course_tracker.csv").resolve()),
        "notes": [
            "Generated from the client CSV archive only.",
            "Dropbox source document links are preserved as internal source references.",
            "Lesson videos are marked pending; full video rendering should be run in batches after source document extraction.",
        ],
    }
    (output_root / "summary.json").write_text(json.dumps(summary, ensure_ascii=False, indent=2), encoding="utf-8")
    return summary


def load_csv_course_seeds(source: Path) -> list[CsvCourseSeed]:
    source_label = str(source.resolve())
    seeds_by_title: dict[str, CsvCourseSeed] = {}
    used_ids: dict[str, str] = {}
    generated_index = 1

    for row in iter_csv_rows(source):
        title = clean_text(row.get("Course Title", ""))
        if not title or title == "Course Title":
            continue

        seed = seeds_by_title.get(title)
        if seed is None:
            base_id = infer_course_id(title) or f"CSV{generated_index:05d}"
            course_id = unique_course_id(base_id, title, used_ids)
            generated_index += 1
            seed = CsvCourseSeed(
                course_id=course_id,
                title=clean_course_title(title, course_id),
                category=clean_category(row.get("Course category", "")),
                source_file=source_label,
            )
            seeds_by_title[title] = seed

        seed.row_count += 1
        update_seed_metadata(seed, row)
        add_faq(seed, row)
        add_source_documents(seed, row)

    return sorted(seeds_by_title.values(), key=lambda item: item.course_id)


def iter_csv_rows(source: Path):
    if source.suffix.lower() == ".zip":
        with zipfile.ZipFile(source) as archive:
            csv_names = [name for name in archive.namelist() if name.lower().endswith(".csv")]
            for name in sorted(csv_names):
                with archive.open(name) as raw:
                    text = (line.decode("utf-8-sig", errors="replace") for line in raw)
                    yield from csv.DictReader(text)
        return

    with source.open("r", encoding="utf-8-sig", newline="") as handle:
        yield from csv.DictReader(handle)


def update_seed_metadata(seed: CsvCourseSeed, row: dict[str, str]) -> None:
    seed.category = seed.category or clean_category(row.get("Course category", ""))
    seed.course_status = seed.course_status or clean_text(row.get("Course Status (Publish/Draft)", ""))
    seed.level = seed.level or clean_text(row.get("Course Level", ""))
    seed.price = seed.price or clean_price(row.get("Price", ""))
    seed.sale_price = seed.sale_price or clean_price(row.get("Sale Price", ""))
    seed.one_time_purchase = seed.one_time_purchase or clean_text(row.get("One-time purchase", ""))
    seed.not_in_membership = seed.not_in_membership or clean_text(row.get("Not included in membership", ""))
    seed.course_views = seed.course_views or clean_text(row.get("Course Views", ""))
    seed.current_students = seed.current_students or clean_text(row.get("Current students", ""))
    seed.duration_info = seed.duration_info or clean_text(row.get("Duration info", ""))
    seed.video_duration = seed.video_duration or clean_text(row.get("Video Duration", ""))
    seed.excerpt = seed.excerpt or useful_text(row.get("Excerpt", ""))
    seed.announcement = seed.announcement or useful_text(row.get("Announcement", ""))
    seed.course_content = seed.course_content or useful_text(row.get("Course Content", ""))
    external_file = clean_text(row.get("External File", ""))
    if external_file and external_file != "External File":
        seed.external_files[external_file] += 1


def add_faq(seed: CsvCourseSeed, row: dict[str, str]) -> None:
    question = useful_text(row.get("FAQ", ""))
    answer = useful_text(row.get("FAQ Answer", ""))
    if not question or not answer:
        return

    pair = {"question": question, "answer": answer}
    if pair not in seed.faqs:
        seed.faqs.append(pair)


def add_source_documents(seed: CsvCourseSeed, row: dict[str, str]) -> None:
    fields = [
        row.get("Lesson Content", ""),
        row.get("Course Content", ""),
        row.get("Lesson video URL", ""),
        row.get("Lesson materials File Title", ""),
    ]
    urls = []
    for field in fields:
        for url in URL_RE.findall(str(field or "")):
            url = url.rstrip(".,);]")
            if "dropbox.com" in url.lower():
                urls.append(url)

    for url in urls:
        if url in seed._source_urls:
            continue
        seed._source_urls.add(url)
        filename = filename_from_url(url)
        lesson_title = useful_lesson_title(row.get("Lesson Title", ""))
        title = lesson_title or title_from_filename(filename)
        seed.source_documents.append(
            CsvSourceDocument(
                url=url,
                filename=filename,
                title=title,
                external_file=clean_text(row.get("External File", "")),
                row_category=clean_category(row.get("Course category", "")),
                course_content=useful_text(row.get("Course Content", "")),
                lesson_title=lesson_title,
                lesson_content=useful_text_without_url(row.get("Lesson Content", "")),
            )
        )


def build_course_package(seed: CsvCourseSeed, max_lessons_per_course: int = 0) -> dict[str, object]:
    source_documents = seed.source_documents
    if max_lessons_per_course > 0:
        source_documents = source_documents[:max_lessons_per_course]

    modules = []
    overview_lesson = build_overview_lesson(seed)
    if overview_lesson:
        modules.append(
            {
                "module_id": "M01",
                "title": "Course Overview",
                "summary": f"Overview and learner orientation for {seed.title}.",
                "lessons": [overview_lesson],
            }
        )

    document_lessons = [
        build_source_document_lesson(seed, document, index + 1)
        for index, document in enumerate(source_documents)
    ]
    if document_lessons:
        modules.append(
            {
                "module_id": "M02" if modules else "M01",
                "title": "Source Learning Materials",
                "summary": f"{len(document_lessons)} learning materials are included for this course.",
                "lessons": document_lessons,
            }
        )

    if not modules:
        modules.append(
            {
                "module_id": "M01",
                "title": "Course Overview",
                "summary": f"Overview for {seed.title}.",
                "lessons": [build_fallback_lesson(seed)],
            }
        )

    lessons = [lesson for module in modules for lesson in module["lessons"]]
    quiz_count = sum(len(lesson.get("quiz", {}).get("questions", [])) for lesson in lessons)
    word_count = count_words(seed.course_content or seed.excerpt or seed.title)
    return {
        "schema_version": "course_automation.course.v1",
        "generator": "csv_registry_v1",
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "course_id": seed.course_id,
        "title": seed.title,
        "category": seed.category or "Short Course",
        "source_file": seed.source_file,
        "source_word_count": word_count,
        "overview": build_overview(seed),
        "objectives": build_objectives(seed),
        "modules": modules,
        "lesson_count": len(lessons),
        "topic_count": sum(len(lesson.get("topics", [])) for lesson in lessons),
        "video_status": "pending_source_extraction",
        "estimated_video_minutes": sum(int(lesson.get("duration_minutes") or 0) for lesson in lessons),
        "totals": {
            "modules": len(modules),
            "lessons": len(lessons),
            "video_scenes": 0,
            "quiz_questions": quiz_count,
            "source_documents": len(seed.source_documents),
        },
        "pricing": {
            "price": seed.price,
            "sale_price": seed.sale_price,
            "one_time_purchase": seed.one_time_purchase,
            "not_in_membership": seed.not_in_membership,
        },
        "csv_metadata": {
            "row_count": seed.row_count,
            "course_status": seed.course_status,
            "level": seed.level,
            "course_views": seed.course_views,
            "current_students": seed.current_students,
            "duration_info": seed.duration_info,
            "video_duration": seed.video_duration,
            "external_files": [name for name, _count in seed.external_files.most_common()],
            "faqs": seed.faqs,
            "source_documents": [
                {
                    "title": document.title,
                    "filename": document.filename,
                    "url": document.url,
                    "external_file": document.external_file,
                    "row_category": document.row_category,
                }
                for document in seed.source_documents
            ],
        },
        "next_stage": {
            "content_rewrite": "csv_registry_generated",
            "source_document_download": "pending",
            "script_generation": "pending_source_extraction",
            "quiz_generation": "draft_generated",
            "tts_generation": "pending",
            "video_rendering": "pending",
            "masterstudy_publish": "review_before_bulk_publish",
        },
        "notes": [
            "This course was generated from the client CSV archive.",
            "Dropbox source document links are stored as internal metadata for the next extraction pass.",
            "Run source document download and lesson enhancement before public learner release.",
        ],
    }


def build_overview_lesson(seed: CsvCourseSeed) -> dict[str, object] | None:
    content = seed.course_content or seed.excerpt
    if not content:
        return None

    return {
        "lesson_id": "M01-L01",
        "title": "Course Introduction",
        "lesson_type": "text",
        "duration_minutes": estimate_duration_minutes(count_words(content)),
        "learning_outcome": f"Describe the purpose and key focus areas of {seed.title}.",
        "study_material": paragraph_blocks(content),
        "source_body": [content],
        "topics": [],
        "source_documents": [],
        "video": planned_video(),
        "quiz": build_quiz(seed.title),
    }


def build_source_document_lesson(seed: CsvCourseSeed, document: CsvSourceDocument, index: int) -> dict[str, object]:
    content = document.lesson_content or document.course_content
    if content:
        material = paragraph_blocks(content)
    else:
        material = [
            {
                "type": "paragraph",
                "text": f"This lesson introduces {document.title} as part of {seed.title}.",
            },
            {
                "type": "paragraph",
                "text": "Use the lesson notes and knowledge check to focus on the main ideas, practical context, and workplace application.",
            },
        ]

    return {
        "lesson_id": f"M02-L{index:03d}",
        "title": document.title,
        "lesson_type": "text",
        "duration_minutes": estimate_duration_minutes(count_words(" ".join(block["text"] for block in material))),
        "learning_outcome": f"Explain the key ideas connected to {document.title}.",
        "study_material": material,
        "source_body": [block["text"] for block in material],
        "topics": [],
        "source_documents": [
            {
                "title": document.title,
                "filename": document.filename,
                "url": document.url,
                "external_file": document.external_file,
                "row_category": document.row_category,
            }
        ],
        "video": planned_video(),
        "quiz": build_quiz(document.title),
    }


def build_fallback_lesson(seed: CsvCourseSeed) -> dict[str, object]:
    text = f"This course introduces {seed.title} and prepares learners to work through the key learning materials and knowledge checks."
    return {
        "lesson_id": "M01-L01",
        "title": "Course Introduction",
        "lesson_type": "text",
        "duration_minutes": 2,
        "learning_outcome": f"Describe the purpose of {seed.title}.",
        "study_material": [{"type": "paragraph", "text": text}],
        "source_body": [text],
        "topics": [],
        "source_documents": [],
        "video": planned_video(),
        "quiz": build_quiz(seed.title),
    }


def build_overview(seed: CsvCourseSeed) -> str:
    if seed.excerpt:
        return seed.excerpt
    if seed.course_content:
        return truncate_words(seed.course_content, 80)
    return (
        f"This course introduces {seed.title}. Learners work through the key learning materials, "
        "practical concepts, and knowledge checks connected to this course."
    )


def build_objectives(seed: CsvCourseSeed) -> list[str]:
    return [
        f"Understand the key requirements covered in {seed.title}.",
        "Work through the learning materials in a structured order.",
        "Prepare for assessment using the course notes and knowledge checks.",
    ]


def build_quiz(title: str) -> dict[str, object]:
    return {
        "status": "draft_generated",
        "questions": [
            {
                "type": "multiple_choice",
                "question": f"What should a learner focus on in {title}?",
                "options": [
                    "The core knowledge, practical steps, and workplace application covered in the lesson.",
                    "Only the file name of the source document.",
                    "Unrelated personal notes.",
                    "Skipping the learner materials.",
                ],
                "answer_index": 0,
            }
        ],
    }


def planned_video() -> dict[str, object]:
    return {
        "status": "pending_source_extraction",
        "format": "narrated_slides",
        "target_minutes": 2,
        "relative_path": "",
        "source_path": "",
        "narration_script": "",
        "scenes": [],
    }


def build_tracker_row(seed: CsvCourseSeed, package: dict[str, object]) -> dict[str, str]:
    return {
        "Course ID": seed.course_id,
        "Course Name": seed.title,
        "Category": seed.category or "Short Course",
        "Source File": seed.source_file,
        "Source Status": "csv_source_found",
        "Extracted": "csv_indexed",
        "Word Count": str(package.get("source_word_count") or 0),
        "Section Count": str(len(package.get("modules", []))),
        "Content Updated": "csv_registry_generated",
        "Script Generated": "pending_source_extraction",
        "Audio Generated": "pending",
        "Video Generated": "pending",
        "Quiz Added": "draft_generated",
        "MasterStudy Added": "pending",
        "Coursebox Status": "csv_metadata_ready",
        "QA Status": "needs_source_extraction_before_final_release",
        "Live URL": "",
        "Date Completed": "",
        "Error/Notes": f"{package.get('lesson_count', 0)} generated lessons; {len(seed.source_documents)} source documents indexed",
    }


def write_tracker(rows: list[dict[str, str]], output_path: Path) -> None:
    with output_path.open("w", newline="", encoding="utf-8-sig") as handle:
        writer = csv.DictWriter(handle, fieldnames=TRACKER_COLUMNS)
        writer.writeheader()
        writer.writerows(rows)


def write_source_documents(rows: list[dict[str, str]], output_path: Path) -> None:
    columns = [
        "Course ID",
        "Course Title",
        "Category",
        "Document Title",
        "Document File",
        "Document URL",
        "External File",
        "Row Category",
    ]
    with output_path.open("w", newline="", encoding="utf-8-sig") as handle:
        writer = csv.DictWriter(handle, fieldnames=columns)
        writer.writeheader()
        writer.writerows(rows)


def paragraph_blocks(text: str) -> list[dict[str, str]]:
    blocks = []
    for chunk in chunk_text(text):
        blocks.append({"type": "paragraph", "text": chunk})
    return blocks


def chunk_text(text: str, max_chars: int = 850) -> list[str]:
    text = clean_text(text)
    if not text:
        return []
    if len(text) <= max_chars:
        return [text]

    sentences = re.split(r"(?<=[.!?])\s+", text)
    chunks: list[str] = []
    current = ""
    for sentence in sentences:
        candidate = f"{current} {sentence}".strip()
        if len(candidate) <= max_chars:
            current = candidate
            continue
        if current:
            chunks.append(current)
        current = sentence
    if current:
        chunks.append(current)
    return chunks


def infer_course_id(title: str) -> str:
    match = COURSE_ID_RE.search(title.strip())
    if match:
        return clean_identifier(match.group(1))
    return ""


def unique_course_id(base_id: str, title: str, used_ids: dict[str, str]) -> str:
    clean_id = clean_identifier(base_id)[:48] or "CSVCOURSE"
    candidate = clean_id
    index = 2
    while candidate in used_ids and used_ids[candidate] != title:
        suffix = f"-{index}"
        candidate = f"{clean_id[:48 - len(suffix)]}{suffix}"
        index += 1
    used_ids[candidate] = title
    return candidate


def clean_course_title(title: str, course_id: str) -> str:
    title = clean_text(title)
    title = re.sub(rf"^{re.escape(course_id)}\s*[-:]\s*", "", title, flags=re.IGNORECASE)
    return title or course_id


def clean_identifier(value: str) -> str:
    value = clean_text(value).upper()
    value = re.sub(r"[^A-Z0-9._-]+", "-", value)
    value = re.sub(r"-{2,}", "-", value).strip("-._")
    return value


def clean_category(value: str | None) -> str:
    value = clean_text(value or "")
    lowered = value.lower()
    if lowered in {"short course", "short courses"}:
        return "Short Course"
    if (
        not value
        or value == "Course category"
        or lowered in DOCUMENT_CATEGORY_VALUES
        or re.fullmatch(r"[\d,./ $-]+", value)
    ):
        return "Short Course"
    return value


def clean_price(value: str | None) -> str:
    value = clean_text(value or "")
    if re.fullmatch(r"\d+(?:\.\d+)?", value):
        return value
    return ""


def useful_lesson_title(value: str | None) -> str:
    value = clean_text(value or "")
    if value.lower() in GENERIC_LESSON_TITLES:
        return ""
    if URL_RE.search(value):
        return ""
    return value


def useful_text(value: str | None) -> str:
    value = clean_text(value or "")
    if not value or value in {"Course Content", "Excerpt", "Announcement", "FAQ", "FAQ Answer"}:
        return ""
    if URL_RE.fullmatch(value):
        return ""
    if PLACEHOLDER_RE.fullmatch(value):
        return ""
    return value


def useful_text_without_url(value: str | None) -> str:
    value = clean_text(value or "")
    if not value:
        return ""
    value = URL_RE.sub("", value)
    return useful_text(value)


def filename_from_url(url: str) -> str:
    path = unquote(urlparse(url).path)
    name = Path(path).name
    return name or "source-document"


def title_from_filename(filename: str) -> str:
    title = re.sub(r"\.[A-Za-z0-9]{2,5}$", "", filename)
    title = re.sub(r"[-_]+", " ", title)
    title = clean_text(title)
    match = re.match(r"^([A-Z]{2,}[A-Z0-9]*\d[A-Z0-9]*)(?:\s+(.+))?$", title, re.IGNORECASE)
    if match and match.group(2):
        return f"{match.group(1).upper()} - {match.group(2).title()}"
    return title.title() if title else "Source Document"


def estimate_duration_minutes(word_count: int) -> int:
    if word_count <= 0:
        return 2
    return max(2, min(8, round(word_count / 160)))


def truncate_words(text: str, limit: int) -> str:
    words = clean_text(text).split()
    if len(words) <= limit:
        return " ".join(words)
    return " ".join(words[:limit]).rstrip(".,;:") + "."


def count_words(text: str) -> int:
    return len(WORD_RE.findall(text))


def clean_text(text: str | None) -> str:
    return re.sub(r"\s+", " ", str(text or "").replace("\u00a0", " ")).strip()
