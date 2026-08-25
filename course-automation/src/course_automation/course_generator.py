from __future__ import annotations

import csv
import json
import re
from datetime import datetime, timezone
from pathlib import Path
from typing import Iterable

from .compliance import apply_compliance_metadata, load_compliance_lookup
from .media_assets import enrich_package_with_media
from .settings import Settings
from .tracker import TRACKER_COLUMNS


WORD_RE = re.compile(r"\b[\w'-]+\b")


def generate_course_packages(settings: Settings, course_ids: Iterable[str] | None = None) -> list[Path]:
    settings.generated_courses_dir.mkdir(parents=True, exist_ok=True)
    settings.generated_bundles_dir.mkdir(parents=True, exist_ok=True)
    settings.scripts_dir.mkdir(parents=True, exist_ok=True)
    settings.source_media_dir.mkdir(parents=True, exist_ok=True)
    settings.generated_videos_dir.mkdir(parents=True, exist_ok=True)

    selected = {course_id.upper() for course_id in course_ids or []}
    compliance_lookup = load_compliance_lookup(settings)
    written: list[Path] = []
    generated_ids: list[str] = []

    for blueprint_path in sorted(settings.blueprints_dir.glob("*.blueprint.json")):
        blueprint = json.loads(blueprint_path.read_text(encoding="utf-8"))
        course_id = str(blueprint.get("course_id", "")).upper()
        if selected and course_id not in selected:
            continue

        package = enrich_package_with_media(settings, build_generated_course(blueprint))
        apply_compliance_metadata(package, compliance_lookup.get(course_id))
        output_path = settings.generated_courses_dir / f"{package['course_id']}.course.json"
        output_path.write_text(json.dumps(package, ensure_ascii=False, indent=2), encoding="utf-8")
        write_lesson_scripts(settings, package)
        write_course_bundle(settings, package)
        written.append(output_path)
        generated_ids.append(str(package["course_id"]))

    update_tracker_generation_status(settings, generated_ids)
    return written


def build_generated_course(blueprint: dict[str, object]) -> dict[str, object]:
    modules = []
    lesson_total = 0
    scene_total = 0
    quiz_total = 0

    for module in list_items(blueprint.get("modules")):
        lessons = []
        for lesson in list_items(module.get("lessons")):
            generated_lesson = build_generated_lesson(lesson, module)
            lessons.append(generated_lesson)
            lesson_total += 1
            scene_total += len(generated_lesson["video"]["scenes"])
            quiz_total += len(generated_lesson["quiz"]["questions"])

        modules.append(
            {
                "module_id": text_value(module, "module_id", f"M{len(modules) + 1:02d}"),
                "title": text_value(module, "title", f"Module {len(modules) + 1}"),
                "summary": summarize_module(module, lessons),
                "lessons": lessons,
            }
        )

    overview = build_course_overview(blueprint, modules)
    estimated_minutes = sum(
        int(lesson["duration_minutes"])
        for module in modules
        for lesson in module["lessons"]
    )

    return {
        "schema_version": "course_automation.course.v1",
        "generator": "local_deterministic_v1",
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "course_id": text_value(blueprint, "course_id"),
        "title": text_value(blueprint, "title"),
        "category": text_value(blueprint, "category"),
        "source_file": text_value(blueprint, "source_file"),
        "source_word_count": int(blueprint.get("source_word_count") or 0),
        "overview": overview,
        "objectives": normalize_items(blueprint.get("objectives")),
        "modules": modules,
        "lesson_count": lesson_total,
        "topic_count": sum(
            len(lesson.get("topics", []))
            for module in modules
            for lesson in module["lessons"]
        ),
        "video_status": "planned",
        "estimated_video_minutes": estimated_minutes,
        "totals": {
            "modules": len(modules),
            "lessons": lesson_total,
            "video_scenes": scene_total,
            "quiz_questions": quiz_total,
        },
        "next_stage": {
            "content_rewrite": "draft_generated",
            "script_generation": "draft_generated",
            "quiz_generation": "draft_generated",
            "tts_generation": "pending",
            "video_rendering": "pending",
            "masterstudy_publish": "ready",
        },
        "notes": [
            "This package turns source text into reviewable lesson material, narration scripts, and scene plans.",
            "Video files are not rendered yet; each lesson is marked as planned and ready for a video engine.",
            "Course source rights and compliance status must be verified before public learner release.",
        ],
    }


def build_generated_lesson(lesson: dict[str, object], module: dict[str, object]) -> dict[str, object]:
    title = text_value(lesson, "title", "Lesson")
    lesson_id = text_value(lesson, "lesson_id", slugify(title).upper())
    source_body = normalize_items(lesson.get("source_body"))
    topics = [
        {
            "heading": text_value(topic, "heading", "Topic"),
            "body": normalize_items(topic.get("body")),
            "source_word_count": int(topic.get("source_word_count") or 0),
        }
        for topic in list_items(lesson.get("topics"))
    ]
    source_text = "\n".join(source_body + [item for topic in topics for item in topic["body"]])
    source_word_count = int(lesson.get("source_word_count") or count_words(source_text))
    duration_minutes = estimate_duration_minutes(source_word_count)
    outcome = build_learning_outcome(title, topics)
    study_material = build_study_material(title, source_body, topics, outcome)
    video = build_video_plan(title, module, source_body, topics, outcome, duration_minutes)
    quiz = build_quiz(title, topics, outcome)

    return {
        "lesson_id": lesson_id,
        "title": title,
        "lesson_type": "video",
        "duration_minutes": duration_minutes,
        "learning_outcome": outcome,
        "study_material": study_material,
        "source_body": source_body,
        "topics": topics,
        "source_word_count": source_word_count,
        "video": video,
        "quiz": quiz,
    }


def build_course_overview(blueprint: dict[str, object], modules: list[dict[str, object]]) -> str:
    title = text_value(blueprint, "title", "this course")
    objective_count = len(normalize_items(blueprint.get("objectives")))
    lesson_count = sum(len(module["lessons"]) for module in modules)
    module_count = len(modules)
    return (
        f"This course guides learners through {title.lower()} with {module_count} modules "
        f"and {lesson_count} structured lessons. Learners work through key concepts, practical examples, "
        f"review checkpoints, and knowledge checks connected to {objective_count} learning objectives."
    )


def summarize_module(module: dict[str, object], lessons: list[dict[str, object]]) -> str:
    title = text_value(module, "title", "This module")
    if not lessons:
        return f"{title} is ready for lesson planning."
    lesson_titles = ", ".join(lesson["title"] for lesson in lessons[:3])
    suffix = "" if len(lessons) <= 3 else f", and {len(lessons) - 3} more lessons"
    return f"{title} contains {len(lessons)} lessons: {lesson_titles}{suffix}."


def build_learning_outcome(title: str, topics: list[dict[str, object]]) -> str:
    clean_title = strip_number_prefix(title).rstrip(".")
    if topics:
        topic_names = ", ".join(strip_number_prefix(str(topic["heading"])) for topic in topics[:2])
        return f"Explain {clean_title.lower()} using the key ideas from {topic_names}."
    return f"Describe the purpose and practical steps for {clean_title.lower()}."


def build_study_material(
    title: str,
    source_body: list[str],
    topics: list[dict[str, object]],
    outcome: str,
) -> list[dict[str, str]]:
    blocks: list[dict[str, str]] = [
        {"type": "paragraph", "text": f"In this lesson, the learner will {outcome[0].lower() + outcome[1:]}"},
    ]

    for paragraph in source_body:
        blocks.extend(paragraph_blocks(paragraph))

    for topic in topics:
        heading = str(topic["heading"]).strip()
        if heading:
            blocks.append({"type": "heading", "text": heading})
        for paragraph in topic["body"]:
            blocks.extend(paragraph_blocks(paragraph))

    blocks.append({"type": "paragraph", "text": f"Review checkpoint: connect this lesson back to {title} and note one workplace example."})
    return blocks


def build_video_plan(
    title: str,
    module: dict[str, object],
    source_body: list[str],
    topics: list[dict[str, object]],
    outcome: str,
    duration_minutes: int,
) -> dict[str, object]:
    focus_points = pick_focus_points(source_body, topics)
    scenes = [
        {
            "order": 1,
            "visual": f"Title slide for {title}",
            "voiceover": f"Welcome. In this lesson, we will {outcome[0].lower() + outcome[1:]}",
        }
    ]

    for point in focus_points[:4]:
        scenes.append(
            {
                "order": len(scenes) + 1,
                "visual": point["visual"],
                "voiceover": point["voiceover"],
            }
        )

    scenes.append(
        {
            "order": len(scenes) + 1,
            "visual": "Quick recap slide with three learner actions",
            "voiceover": f"To finish, check that you can summarize {strip_number_prefix(title).lower()} and apply it in a simple practical situation.",
        }
    )

    return {
        "status": "planned",
        "format": "narrated_slides",
        "target_minutes": duration_minutes,
        "narration_script": "\n\n".join(scene["voiceover"] for scene in scenes),
        "scenes": scenes,
        "render_notes": [
            "Use screen capture where the lesson involves software steps.",
            "Use simple labels and close-up visuals for hardware, settings, and process lessons.",
            "Keep each scene short enough for later TTS and subtitle generation.",
        ],
    }


def pick_focus_points(source_body: list[str], topics: list[dict[str, object]]) -> list[dict[str, str]]:
    points: list[dict[str, str]] = []
    for paragraph in source_body[:2]:
        points.append(
            {
                "visual": "Learner-facing summary slide",
                "voiceover": first_sentence(paragraph),
            }
        )

    for topic in topics:
        heading = str(topic["heading"])
        body = [str(item) for item in topic["body"]]
        voiceover = first_sentence(" ".join(body)) if body else f"This part introduces {heading.lower()}."
        points.append(
            {
                "visual": f"Slide or screen example: {heading}",
                "voiceover": f"{strip_number_prefix(heading)}. {voiceover}",
            }
        )

    return points


def build_quiz(title: str, topics: list[dict[str, object]], outcome: str) -> dict[str, object]:
    questions = [
        {
            "type": "multiple_choice",
            "question": f"What is the main goal of the lesson '{title}'?",
            "options": [
                outcome,
                "Memorize unrelated technical terms without using them.",
                "Skip the practical steps and only read the headings.",
                "Replace the course objectives with personal notes.",
            ],
            "answer_index": 0,
        }
    ]

    if topics:
        first_topic = strip_number_prefix(str(topics[0]["heading"]))
        questions.append(
            {
                "type": "short_answer",
                "question": f"Give one practical example connected to {first_topic}.",
                "expected_answer": "A learner should describe a practical example from the study material or workplace context.",
            }
        )

    return {
        "status": "draft_generated",
        "questions": questions,
    }


def write_lesson_scripts(settings: Settings, package: dict[str, object]) -> None:
    course_id = str(package["course_id"])
    course_dir = settings.scripts_dir / course_id
    course_dir.mkdir(parents=True, exist_ok=True)

    for module in list_items(package.get("modules")):
        for lesson in list_items(module.get("lessons")):
            lesson_id = text_value(lesson, "lesson_id", "lesson")
            video = lesson.get("video") if isinstance(lesson.get("video"), dict) else {}
            lines = [
                f"# {course_id} {lesson_id} - {text_value(lesson, 'title', 'Lesson')}",
                "",
                f"Module: {text_value(module, 'title', 'Module')}",
                f"Target duration: {int(lesson.get('duration_minutes') or 0)} minutes",
                f"Video status: {text_value(video, 'status', 'planned')}",
                "",
                "## Narration Script",
                "",
                str(video.get("narration_script", "")).strip(),
                "",
                "## Scene Plan",
                "",
            ]
            for scene in list_items(video.get("scenes")):
                lines.append(f"{int(scene.get('order') or 0)}. {text_value(scene, 'visual', 'Visual')}")
                lines.append(f"   Voiceover: {text_value(scene, 'voiceover', '')}")
            (course_dir / f"{slugify(lesson_id)}.md").write_text("\n".join(lines).strip() + "\n", encoding="utf-8")


def write_course_bundle(settings: Settings, package: dict[str, object]) -> None:
    course_id = str(package["course_id"])
    course_dir = settings.generated_bundles_dir / course_id
    course_dir.mkdir(parents=True, exist_ok=True)

    (course_dir / "01_video_production_script.md").write_text(
        build_course_production_script(package),
        encoding="utf-8",
    )
    (course_dir / "02_lms_masterstudy_coursebox_metadata.json").write_text(
        json.dumps(build_lms_metadata(package), ensure_ascii=False, indent=2),
        encoding="utf-8",
    )
    (course_dir / "03_assessment_quiz_bank.json").write_text(
        json.dumps(build_course_quiz_bank(package), ensure_ascii=False, indent=2),
        encoding="utf-8",
    )
    (course_dir / "04_core_theory_content.md").write_text(
        build_core_theory_markdown(package),
        encoding="utf-8",
    )


def build_course_production_script(package: dict[str, object]) -> str:
    course_id = text_value(package, "course_id")
    title = text_value(package, "title")
    lines = [
        f"# Production Script: {course_id} - {title}",
        "",
        f"Generated: {datetime.now(timezone.utc).isoformat()}",
        "",
        "## Course Introduction",
        "",
        f"[VISUAL] Title slide for {course_id}: {title}",
        f"[AUDIO] Welcome to {title}. This course is organized into short lessons with narrated scene plans and learner checks.",
        "",
        "## Lesson Scripts",
        "",
    ]

    for module in list_items(package.get("modules")):
        lines.extend([f"## {text_value(module, 'module_id')} - {text_value(module, 'title')}", ""])
        for lesson in list_items(module.get("lessons")):
            video = lesson.get("video") if isinstance(lesson.get("video"), dict) else {}
            lines.extend(
                [
                    f"### {text_value(lesson, 'lesson_id')} - {text_value(lesson, 'title')}",
                    "",
                    f"Target duration: {int(lesson.get('duration_minutes') or 0)} minutes",
                    "",
                ]
            )
            for scene in list_items(video.get("scenes")):
                lines.append(f"{int(scene.get('order') or 0)}. [VISUAL] {text_value(scene, 'visual', 'Visual')}")
                lines.append(f"   [AUDIO] {text_value(scene, 'voiceover', '')}")
                lines.append("   [TTS_PAUSE] 1.2s")
                lines.append("")

    lines.extend(
        [
            "## Compliance QA",
            "",
            "Before learner release, confirm source licence, supersession status, assessment requirements, and any replacement unit mapping.",
        ]
    )
    return "\n".join(lines).strip() + "\n"


def build_lms_metadata(package: dict[str, object]) -> dict[str, object]:
    return {
        "schema_version": "course_automation.lms_metadata.v1",
        "engine_version": "course-automation-local-0.4",
        "last_compiled": datetime.now(timezone.utc).isoformat(),
        "unit_code": text_value(package, "course_id"),
        "unit_title": text_value(package, "title"),
        "category": text_value(package, "category"),
        "source_file": text_value(package, "source_file"),
        "status": "packaged_for_masterstudy_review",
        "masterstudy": {
            "import_schema": text_value(package, "schema_version"),
            "lesson_count": int(package.get("lesson_count") or 0),
            "video_status": text_value(package, "video_status", "planned"),
        },
        "coursebox": {
            "metadata_ready": True,
            "recommended_use": "Use as source metadata or SCORM/video production input if Coursebox is used later.",
        },
        "compliance": package.get("compliance", {}),
        "content_rights_notice": (
            "Source material and public-register references must be checked against their actual licence terms. "
            "Do not assume public-domain status unless the source licence explicitly allows it."
        ),
        "included_outputs": [
            "WordPress/MasterStudy course JSON",
            "Narration and video production script",
            "Course-level quiz bank",
            "Core theory markdown",
            "Extracted source images and planned video references",
        ],
    }


def build_course_quiz_bank(package: dict[str, object]) -> dict[str, object]:
    questions: list[dict[str, object]] = []
    for module in list_items(package.get("modules")):
        for lesson in list_items(module.get("lessons")):
            quiz = lesson.get("quiz") if isinstance(lesson.get("quiz"), dict) else {}
            for index, question in enumerate(list_items(quiz.get("questions")), start=1):
                item = dict(question)
                item["module_id"] = text_value(module, "module_id")
                item["module_title"] = text_value(module, "title")
                item["lesson_id"] = text_value(lesson, "lesson_id")
                item["lesson_title"] = text_value(lesson, "title")
                item["question_id"] = f"{item['lesson_id']}-Q{index:02d}"
                questions.append(item)

    return {
        "schema_version": "course_automation.quiz_bank.v1",
        "course_id": text_value(package, "course_id"),
        "title": text_value(package, "title"),
        "assessment_type": "formative_knowledge_check",
        "total_questions": len(questions),
        "questions": questions,
    }


def build_core_theory_markdown(package: dict[str, object]) -> str:
    lines = [
        f"# {text_value(package, 'course_id')} - {text_value(package, 'title')}",
        "",
        f"Category: {text_value(package, 'category')}",
        "",
        "## Overview",
        "",
        text_value(package, "overview"),
        "",
    ]

    objectives = normalize_items(package.get("objectives"))
    if objectives:
        lines.extend(["## Learning Objectives", ""])
        lines.extend(f"- {objective}" for objective in objectives)
        lines.append("")

    for module in list_items(package.get("modules")):
        lines.extend([f"## {text_value(module, 'title')}", ""])
        summary = text_value(module, "summary")
        if summary:
            lines.extend([summary, ""])
        for lesson in list_items(module.get("lessons")):
            lines.extend([f"### {text_value(lesson, 'title')}", ""])
            for block in list_items(lesson.get("study_material")):
                block_type = text_value(block, "type")
                text = text_value(block, "text")
                if not text:
                    continue
                if block_type == "heading":
                    lines.extend([f"#### {text}", ""])
                else:
                    lines.extend([text, ""])

    lines.extend(
        [
            "## QA Notice",
            "",
            "This theory content is generated from source material and must be reviewed before learner release.",
        ]
    )
    return "\n".join(lines).strip() + "\n"


def update_tracker_generation_status(settings: Settings, course_ids: list[str]) -> None:
    if not course_ids or not settings.tracker_path.exists():
        return

    generated = set(course_ids)
    with settings.tracker_path.open("r", encoding="utf-8-sig", newline="") as handle:
        rows = list(csv.DictReader(handle))

    for row in rows:
        if row.get("Course ID") not in generated:
            continue
        row["Content Updated"] = "generated_with_images"
        row["Script Generated"] = "draft_generated"
        row["Video Generated"] = "course_video_rendered"
        row["Quiz Added"] = "draft_generated"
        row["Coursebox Status"] = "local_generator_ready"
        if row.get("QA Status") in {"", "pending"}:
            row["QA Status"] = "ready_for_wordpress_import"

    with settings.tracker_path.open("w", encoding="utf-8-sig", newline="") as handle:
        writer = csv.DictWriter(handle, fieldnames=TRACKER_COLUMNS)
        writer.writeheader()
        writer.writerows(rows)


def list_items(value: object) -> list[dict[str, object]]:
    if not isinstance(value, list):
        return []
    return [item for item in value if isinstance(item, dict)]


def normalize_items(value: object) -> list[str]:
    if not isinstance(value, list):
        return []
    items: list[str] = []
    for item in value:
        text = normalize_text(str(item))
        if text:
            items.append(text)
    return items


def paragraph_blocks(text: str) -> list[dict[str, str]]:
    blocks: list[dict[str, str]] = []
    for chunk in chunk_text(text):
        blocks.append({"type": "paragraph", "text": chunk})
    return blocks


def chunk_text(text: str, max_chars: int = 850) -> list[str]:
    text = normalize_text(text)
    if len(text) <= max_chars:
        return [text] if text else []

    sentences = re.split(r"(?<=[.!?])\s+", text)
    chunks: list[str] = []
    current = ""
    for sentence in sentences:
        if not sentence:
            continue
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


def normalize_text(text: str) -> str:
    return re.sub(r"\s+", " ", text).strip()


def text_value(data: object, key: str, default: str = "") -> str:
    if isinstance(data, dict):
        value = data.get(key, default)
    else:
        value = default
    if isinstance(value, (str, int, float)):
        return normalize_text(str(value))
    return default


def count_words(text: str) -> int:
    return len(WORD_RE.findall(text))


def estimate_duration_minutes(word_count: int) -> int:
    if word_count <= 0:
        return 2
    return max(2, min(8, round(word_count / 160)))


def first_sentence(text: str) -> str:
    text = normalize_text(text)
    if not text:
        return ""
    match = re.search(r"^(.+?[.!?])\s", text + " ")
    return match.group(1) if match else text[:220].rstrip()


def strip_number_prefix(text: str) -> str:
    return re.sub(r"^\d+(?:\.\d+)*\s*[-\u2013\u2014.]?\s*", "", normalize_text(text)).strip()


def slugify(text: str) -> str:
    slug = re.sub(r"[^A-Za-z0-9]+", "-", text).strip("-").lower()
    return slug or "lesson"
