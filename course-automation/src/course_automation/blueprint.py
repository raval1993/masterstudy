from __future__ import annotations

import re
from dataclasses import dataclass, field


@dataclass
class Topic:
    heading: str
    body: list[str]


@dataclass
class Lesson:
    lesson_id: str
    title: str
    source_body: list[str] = field(default_factory=list)
    topics: list[Topic] = field(default_factory=list)


@dataclass
class Module:
    module_id: str
    title: str
    lessons: list[Lesson] = field(default_factory=list)


def build_course_blueprint(record: dict[str, object]) -> dict[str, object]:
    objectives: list[str] = []
    modules: list[Module] = []
    current_module: Module | None = None
    current_lesson: Lesson | None = None

    for section in record.get("sections", []):
        heading = str(section.get("heading", "")).strip()
        level = int(section.get("level", 1))
        body = [str(item).strip() for item in section.get("body", []) if str(item).strip()]

        if heading.lower() == "objectives":
            objectives.extend(split_objectives(body))
            continue

        if is_numbered_level(heading, expected_level=1) and level == 1:
            module_id = f"M{len(modules) + 1:02d}"
            current_module = Module(module_id=module_id, title=heading)
            modules.append(current_module)
            current_lesson = None
            if body:
                current_lesson = Lesson(
                    lesson_id=f"{module_id}-L01",
                    title="Module overview",
                    source_body=body,
                )
                current_module.lessons.append(current_lesson)
            continue

        if current_module is None:
            current_module = Module(module_id="M00", title="Course overview")
            modules.append(current_module)

        if is_numbered_level(heading, expected_level=2) and level <= 2:
            lesson_id = f"{current_module.module_id}-L{len(current_module.lessons) + 1:02d}"
            current_lesson = Lesson(lesson_id=lesson_id, title=heading, source_body=body)
            current_module.lessons.append(current_lesson)
            continue

        if current_lesson is None:
            current_lesson = Lesson(
                lesson_id=f"{current_module.module_id}-L{len(current_module.lessons) + 1:02d}",
                title="Source material",
            )
            current_module.lessons.append(current_lesson)

        current_lesson.topics.append(Topic(heading=heading, body=body))

    module_payloads = [module_to_dict(module) for module in modules]
    return {
        "course_id": record["course_id"],
        "title": record["title"],
        "category": record["category"],
        "source_file": record["source_file"],
        "source_word_count": record["word_count"],
        "estimated_video_minutes": 5,
        "objectives": objectives,
        "modules": module_payloads,
        "lesson_count": sum(len(module["lessons"]) for module in module_payloads),
        "topic_count": sum(
            len(lesson["topics"])
            for module in module_payloads
            for lesson in module["lessons"]
        ),
        "next_stage": {
            "content_rewrite": "pending",
            "script_generation": "pending",
            "quiz_generation": "pending",
            "tts_generation": "pending",
            "video_rendering": "pending",
            "masterstudy_publish": "pending",
        },
        "notes": [
            "Source has been extracted and grouped into modules, lessons, and topics.",
            "Next pass should rewrite each lesson into learner-friendly study material.",
            "A later script pass should compress the full course into approximately 5 minutes of narration.",
        ],
    }


def is_numbered_level(heading: str, expected_level: int) -> bool:
    match = re.match(r"^(\d+(?:\.\d+)*)\b", heading)
    if not match:
        return False
    return len(match.group(1).split(".")) == expected_level


def split_objectives(body: list[str]) -> list[str]:
    objectives: list[str] = []
    for item in body:
        parts = [part.strip(" -\t") for part in re.split(r"\n+", item) if part.strip(" -\t")]
        objectives.extend(parts)
    return objectives


def module_to_dict(module: Module) -> dict[str, object]:
    return {
        "module_id": module.module_id,
        "title": module.title,
        "lessons": [
            {
                "lesson_id": lesson.lesson_id,
                "title": lesson.title,
                "source_body": lesson.source_body,
                "topics": [
                    {
                        "heading": topic.heading,
                        "body": topic.body,
                        "source_word_count": count_words("\n".join(topic.body)),
                    }
                    for topic in lesson.topics
                ],
                "source_word_count": count_words(
                    "\n".join(lesson.source_body)
                    + "\n"
                    + "\n".join("\n".join(topic.body) for topic in lesson.topics)
                ),
            }
            for lesson in module.lessons
        ],
    }


def count_words(text: str) -> int:
    return len(re.findall(r"\b[\w'-]+\b", text))
