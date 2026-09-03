from __future__ import annotations

import argparse
import json
import time
from pathlib import Path

from .blueprint import build_course_blueprint
from .compliance import SOURCE_EXTENSIONS, build_course_registry, scan_training_updates
from .csv_importer import generate_courses_from_csv
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


def run_ingest_pipeline(source: Path, category: str, project_root: Path | None = None) -> int:
    settings = load_settings(project_root)
    ensure_output_dirs(settings.courses_dir, settings.markdown_dir, settings.blueprints_dir)

    records: list[dict[str, object]] = []
    for path in iter_docx_files(source):
        course = read_docx_course(path, category=category)
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
    build_course_registry(source, category, settings.registry_path)

    print(f"wrote registry: {settings.registry_path}")
    print(f"wrote tracker: {settings.tracker_path}")
    print(f"wrote summary: {settings.summary_path}")
    return len(records)


def command_inventory(args: argparse.Namespace) -> int:
    source = Path(args.source).resolve()
    files = iter_docx_files(source)
    print(json.dumps({"source": str(source), "docx_count": len(files), "files": [path.name for path in files]}, indent=2))
    return 0


def command_ingest(args: argparse.Namespace) -> int:
    source = Path(args.source).resolve()
    run_ingest_pipeline(source, args.category, Path(args.project_root).resolve() if args.project_root else None)
    return 0


def command_generate_courses(args: argparse.Namespace) -> int:
    from .course_generator import generate_course_packages

    settings = load_settings(Path(args.project_root).resolve() if args.project_root else None)
    paths = generate_course_packages(settings, args.course_id)
    print(f"generated {len(paths)} course package files")
    for path in paths:
        print(path)
    return 0


def command_generate_from_csv(args: argparse.Namespace) -> int:
    settings = load_settings(Path(args.project_root).resolve() if args.project_root else None)
    output_root = Path(args.output_root).resolve() if args.output_root else settings.processed_dir / "csv"
    summary = generate_courses_from_csv(
        Path(args.source).resolve(),
        output_root,
        limit=max(0, int(args.limit or 0)),
        max_lessons_per_course=max(0, int(args.max_lessons_per_course or 0)),
    )
    print(json.dumps(summary, ensure_ascii=False, indent=2))
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


def command_build_registry(args: argparse.Namespace) -> int:
    settings = load_settings(Path(args.project_root).resolve() if args.project_root else None)
    source = Path(args.source).resolve()
    output = Path(args.output).resolve() if args.output else settings.registry_path
    courses = build_course_registry(source, args.category, output)
    print(f"wrote registry for {len(courses)} courses: {output}")
    return 0


def command_scan_updates(args: argparse.Namespace) -> int:
    settings = load_settings(Path(args.project_root).resolve() if args.project_root else None)
    registry = Path(args.registry).resolve() if args.registry else settings.registry_path
    updates = Path(args.updates).resolve()
    output = Path(args.output).resolve() if args.output else settings.compliance_report_path
    report = scan_training_updates(registry, updates, output, settings)
    print(f"wrote compliance report: {output}")
    print(f"courses checked: {report['total_courses']}")
    print(f"needs review: {report['needs_review']}")
    return 0


def command_watch(args: argparse.Namespace) -> int:
    settings = load_settings(Path(args.project_root).resolve() if args.project_root else None)
    source = Path(args.source).resolve()

    def run(reason: str) -> None:
        from .course_generator import generate_course_packages

        print(f"processing source library ({reason})")
        run_ingest_pipeline(source, args.category, settings.project_root)
        generate_course_packages(settings)
        if args.publish_wordpress:
            publish_args = argparse.Namespace(
                project_root=str(settings.project_root),
                wp_path=args.wp_path,
                php=args.php,
                wp_cli=args.wp_cli,
            )
            command_publish_wordpress(publish_args)
        print("processing complete")

    if args.once:
        run("single run")
        return 0

    run("startup")
    print(f"watching {source} for source updates")
    if run_watchdog_loop(source, args.debounce_seconds, run):
        return 0

    print("watchdog is not installed; using polling watcher")
    return run_polling_loop(source, args.poll_seconds, run)


def run_watchdog_loop(source: Path, debounce_seconds: float, callback) -> bool:
    try:
        from watchdog.events import FileSystemEventHandler
        from watchdog.observers import Observer
    except ImportError:
        return False

    state = {"pending": False, "last_event": 0.0, "reason": ""}

    class Handler(FileSystemEventHandler):
        def on_created(self, event):
            self.schedule(event)

        def on_modified(self, event):
            self.schedule(event)

        def schedule(self, event):
            if event.is_directory:
                return
            path = Path(event.src_path)
            if path.suffix.lower() not in SOURCE_EXTENSIONS or path.name.startswith("~$"):
                return
            state["pending"] = True
            state["last_event"] = time.time()
            state["reason"] = path.name

    observer = Observer()
    observer.schedule(Handler(), str(source), recursive=True)
    observer.start()
    try:
        while True:
            if state["pending"] and time.time() - float(state["last_event"]) >= debounce_seconds:
                reason = str(state["reason"] or "file update")
                state["pending"] = False
                callback(reason)
            time.sleep(1)
    except KeyboardInterrupt:
        observer.stop()
    finally:
        observer.join()
    return True


def run_polling_loop(source: Path, poll_seconds: float, callback) -> int:
    snapshot = snapshot_source_files(source)
    try:
        while True:
            time.sleep(poll_seconds)
            latest = snapshot_source_files(source)
            if latest != snapshot:
                snapshot = latest
                callback("polling change detected")
    except KeyboardInterrupt:
        return 0


def snapshot_source_files(source: Path) -> dict[str, tuple[int, int]]:
    snapshot: dict[str, tuple[int, int]] = {}
    for path in source.rglob("*"):
        if not path.is_file() or path.suffix.lower() not in SOURCE_EXTENSIONS or path.name.startswith("~$"):
            continue
        stat = path.stat()
        snapshot[str(path.resolve())] = (stat.st_size, int(stat.st_mtime))
    return snapshot


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

    csv_generate = subparsers.add_parser("generate-from-csv", help="Generate course packages from a client CSV or CSV zip")
    csv_generate.add_argument("--source", required=True, help="CSV file or zip containing CSV files")
    csv_generate.add_argument("--project-root", default="", help="Project root override")
    csv_generate.add_argument("--output-root", default="", help="Output folder, defaults to data/processed/csv")
    csv_generate.add_argument("--limit", type=int, default=0, help="Optional max course count for testing")
    csv_generate.add_argument(
        "--max-lessons-per-course",
        type=int,
        default=0,
        help="Optional cap for generated source-document lessons per course; 0 means no cap",
    )
    csv_generate.set_defaults(func=command_generate_from_csv)

    publish = subparsers.add_parser("publish-wordpress", help="Push generated blueprints into Laragon WordPress/MasterStudy")
    publish.add_argument("--project-root", default="", help="Project root override")
    publish.add_argument("--wp-path", default="", help="WordPress install path, defaults to C:\\laragon\\www\\lms-masterstudy")
    publish.add_argument("--php", default="", help="PHP executable path, defaults to Laragon PHP")
    publish.add_argument("--wp-cli", default="", help="WP-CLI phar path")
    publish.set_defaults(func=command_publish_wordpress)

    registry = subparsers.add_parser("build-registry", help="Build course_map.json for the source library")
    registry.add_argument("--source", required=True, help="Source directory containing course source files")
    registry.add_argument("--category", required=True, help="Course category name")
    registry.add_argument("--project-root", default="", help="Project root override")
    registry.add_argument("--output", default="", help="Registry JSON output path")
    registry.set_defaults(func=command_build_registry)

    updates = subparsers.add_parser("scan-updates", help="Compare course_map.json against a Training.gov.au CSV/JSON export")
    updates.add_argument("--updates", required=True, help="Training update CSV/JSON export path")
    updates.add_argument("--registry", default="", help="Registry path, defaults to processed course_map.json")
    updates.add_argument("--project-root", default="", help="Project root override")
    updates.add_argument("--output", default="", help="Compliance report JSON output path")
    updates.set_defaults(func=command_scan_updates)

    watch = subparsers.add_parser("watch", help="Watch a source folder and run the pipeline when files change")
    watch.add_argument("--source", required=True, help="Source directory containing course DOCX files")
    watch.add_argument("--category", required=True, help="Course category name")
    watch.add_argument("--project-root", default="", help="Project root override")
    watch.add_argument("--publish-wordpress", action="store_true", help="Publish into local WordPress after generation")
    watch.add_argument("--wp-path", default="", help="WordPress install path")
    watch.add_argument("--php", default="", help="PHP executable path")
    watch.add_argument("--wp-cli", default="", help="WP-CLI phar path")
    watch.add_argument("--poll-seconds", type=float, default=10.0, help="Polling interval when watchdog is unavailable")
    watch.add_argument("--debounce-seconds", type=float, default=3.0, help="Delay after file events before processing")
    watch.add_argument("--once", action="store_true", help="Run one watch-cycle process and exit")
    watch.set_defaults(func=command_watch)

    return parser


def main() -> int:
    parser = build_parser()
    args = parser.parse_args()
    return args.func(args)


if __name__ == "__main__":
    raise SystemExit(main())
