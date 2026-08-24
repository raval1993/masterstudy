from __future__ import annotations

import argparse
import json
from pathlib import Path

from .blueprint import build_course_blueprint
from .course_generator import generate_course_packages
from .docx_reader import read_docx_course
from .settings import load_settings
from .tracker import (
    build_tracker_rows,
    course_to_record,
    ensure_output_dirs,
    write_course_json,
    write_course_markdown,
    write_blueprint,
    write_summary,
    write_tracker,
)
from .wordpress_publisher import (
    default_wp_cli,
    default_wp_path,
    fetch_published_courses,
    find_laragon_php,
    import_blueprints,
    sync_blueprints,
    sync_course_packages,
    sync_media_assets,
    sync_videos,
    update_tracker_with_published_courses,
)


def iter_docx_files(source: Path) -> list[Path]:
    return sorted(path for path in source.rglob("*.docx") if not path.name.startswith("~$"))


def command_inventory(args: argparse.Namespace) -> int:
    source = Path(args.source).resolve()
    files = iter_docx_files(source)
    print(json.dumps({"source": str(source), "docx_count": len(files), "files": [path.name for path in files]}, indent=2))
    return 0


def command_ingest(args: argparse.Namespace) -> int:
    source = Path(args.source).resolve()
    settings = load_settings(Path(args.project_root).resolve() if args.project_root else None)
    ensure_output_dirs(settings.courses_dir, settings.markdown_dir, settings.blueprints_dir)

    records: list[dict[str, object]] = []
    for path in iter_docx_files(source):
        course = read_docx_course(path, category=args.category)
        record = course_to_record(course, path)
        records.append(record)
        write_course_json(record, settings.courses_dir / f"{course.course_id}.json")
        write_course_markdown(record, settings.markdown_dir / f"{course.course_id}.md")
        blueprint = build_course_blueprint(record)
        write_blueprint(blueprint, settings.blueprints_dir / f"{course.course_id}.blueprint.json")
        print(f"extracted {course.course_id}: {course.title} ({course.word_count} words)")

    rows = build_tracker_rows(records)
    write_tracker(rows, settings.tracker_path)
    write_summary(records, settings.summary_path)

    print(f"wrote tracker: {settings.tracker_path}")
    print(f"wrote summary: {settings.summary_path}")
    return 0


def command_generate_courses(args: argparse.Namespace) -> int:
    settings = load_settings(Path(args.project_root).resolve() if args.project_root else None)
    paths = generate_course_packages(settings, args.course_id)
    print(f"generated {len(paths)} course package files")
    for path in paths:
        print(path)
    return 0


def command_publish_wordpress(args: argparse.Namespace) -> int:
    settings = load_settings(Path(args.project_root).resolve() if args.project_root else None)
    php = Path(args.php).resolve() if args.php else find_laragon_php()
    wp_cli = Path(args.wp_cli).resolve() if args.wp_cli else default_wp_cli(settings)
    wp_path = Path(args.wp_path).resolve() if args.wp_path else default_wp_path()

    copied_courses = sync_course_packages(settings, wp_path)
    copied_media = sync_media_assets(settings, wp_path)
    copied_videos = sync_videos(settings, wp_path)
    copied_blueprints = sync_blueprints(settings, wp_path)
    print(f"copied {copied_courses} generated course package files into WordPress")
    print(f"copied {copied_media} extracted media files into WordPress")
    print(f"copied {copied_videos} generated video files into WordPress")
    print(f"copied {copied_blueprints} blueprint files into WordPress")

    output = import_blueprints(php, wp_cli, wp_path)
    if output:
        print(output)

    courses = fetch_published_courses(php, wp_cli, wp_path)
    update_tracker_with_published_courses(settings, courses)
    print(f"tracker updated for {len(courses)} WordPress course records")
    for course in courses:
        print(f"{course.course_id}: post {course.post_id} ({course.post_status}) {course.post_title}")

    return 0


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Course automation utilities")
    subparsers = parser.add_subparsers(dest="command", required=True)

    inventory = subparsers.add_parser("inventory", help="List source DOCX files")
    inventory.add_argument("--source", required=True, help="Source directory containing course DOCX files")
    inventory.set_defaults(func=command_inventory)

    ingest = subparsers.add_parser("ingest", help="Extract DOCX courses and create tracker outputs")
    ingest.add_argument("--source", required=True, help="Source directory containing course DOCX files")
    ingest.add_argument("--category", required=True, help="Course category name")
    ingest.add_argument("--project-root", default="", help="Project root override")
    ingest.set_defaults(func=command_ingest)

    generate = subparsers.add_parser("generate-courses", help="Generate learner-ready courses, scripts, and video plans")
    generate.add_argument("--project-root", default="", help="Project root override")
    generate.add_argument("--course-id", action="append", default=[], help="Course ID to generate; can be repeated")
    generate.set_defaults(func=command_generate_courses)

    publish = subparsers.add_parser("publish-wordpress", help="Push generated blueprints into Laragon WordPress/MasterStudy")
    publish.add_argument("--project-root", default="", help="Project root override")
    publish.add_argument("--wp-path", default="", help="WordPress install path, defaults to C:\\laragon\\www\\lms-masterstudy")
    publish.add_argument("--php", default="", help="PHP executable path, defaults to Laragon PHP")
    publish.add_argument("--wp-cli", default="", help="WP-CLI phar path")
    publish.set_defaults(func=command_publish_wordpress)

    return parser


def main() -> int:
    parser = build_parser()
    args = parser.parse_args()
    return args.func(args)


if __name__ == "__main__":
    raise SystemExit(main())
