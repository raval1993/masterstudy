from __future__ import annotations

import os
from dataclasses import dataclass
from pathlib import Path


@dataclass(frozen=True)
class Settings:
    project_root: Path
    data_dir: Path
    processed_dir: Path
    courses_dir: Path
    blueprints_dir: Path
    generated_courses_dir: Path
    markdown_dir: Path
    source_media_dir: Path
    scripts_dir: Path
    generated_videos_dir: Path
    tracker_path: Path
    summary_path: Path


def find_project_root(start: Path | None = None) -> Path:
    package_root = Path(__file__).resolve().parents[2]
    starts = [(start or Path.cwd()).resolve(), package_root]
    seen: set[Path] = set()
    for current in starts:
        for candidate in [current, *current.parents]:
            if candidate in seen:
                continue
            seen.add(candidate)
            if (candidate / "src" / "course_automation").exists():
                return candidate
    return package_root


def load_settings(project_root: Path | None = None) -> Settings:
    root = (project_root or find_project_root()).resolve()
    data_dir = Path(os.environ.get("COURSE_AUTOMATION_DATA_DIR", root / "data")).resolve()
    processed_dir = data_dir / "processed"
    return Settings(
        project_root=root,
        data_dir=data_dir,
        processed_dir=processed_dir,
        courses_dir=processed_dir / "courses",
        blueprints_dir=processed_dir / "blueprints",
        generated_courses_dir=processed_dir / "generated" / "courses",
        markdown_dir=processed_dir / "markdown",
        source_media_dir=processed_dir / "media" / "source",
        scripts_dir=processed_dir / "generated" / "scripts",
        generated_videos_dir=processed_dir / "generated" / "videos",
        tracker_path=processed_dir / "course_tracker.csv",
        summary_path=processed_dir / "summary.json",
    )
