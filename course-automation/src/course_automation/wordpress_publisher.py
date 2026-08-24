from __future__ import annotations

import csv
import json
import shutil
import subprocess
from dataclasses import dataclass
from pathlib import Path

from .settings import Settings
from .tracker import TRACKER_COLUMNS


@dataclass(frozen=True)
class PublishedCourse:
    course_id: str
    post_id: int
    post_title: str
    post_status: str
    edit_url: str
    permalink: str


def find_laragon_php() -> Path:
    php_root = Path(r"C:\laragon\bin\php")
    candidates = sorted(php_root.glob("php-*/php.exe"), reverse=True)
    if not candidates:
        raise FileNotFoundError("Could not find Laragon php.exe under C:\\laragon\\bin\\php")
    return candidates[0]


def default_wp_cli(settings: Settings) -> Path:
    wp_cli = settings.project_root.parent / "wordpress-masterstudy" / "tools" / "wp-cli.phar"
    if not wp_cli.exists():
        raise FileNotFoundError(f"WP-CLI not found: {wp_cli}")
    return wp_cli


def default_wp_path() -> Path:
    wp_path = Path(r"C:\laragon\www\lms-masterstudy")
    if not (wp_path / "wp-config.php").exists():
        raise FileNotFoundError(f"Laragon WordPress site not found: {wp_path}")
    return wp_path


def sync_blueprints(settings: Settings, wp_path: Path) -> int:
    source = settings.blueprints_dir
    destination = wp_path / "wp-content" / "course-automation" / "blueprints"
    destination.mkdir(parents=True, exist_ok=True)

    count = 0
    for path in sorted(source.glob("*.blueprint.json")):
        shutil.copy2(path, destination / path.name)
        count += 1
    return count


def sync_course_packages(settings: Settings, wp_path: Path) -> int:
    source = settings.generated_courses_dir
    destination = wp_path / "wp-content" / "course-automation" / "courses"
    destination.mkdir(parents=True, exist_ok=True)

    count = 0
    if not source.exists():
        return count

    for path in sorted(source.glob("*.course.json")):
        shutil.copy2(path, destination / path.name)
        count += 1
    return count


def sync_media_assets(settings: Settings, wp_path: Path) -> int:
    return sync_tree_files(settings.source_media_dir, wp_path / "wp-content" / "course-automation" / "media")


def sync_videos(settings: Settings, wp_path: Path) -> int:
    return sync_tree_files(settings.generated_videos_dir, wp_path / "wp-content" / "course-automation" / "videos")


def sync_tree_files(source: Path, destination: Path) -> int:
    destination.mkdir(parents=True, exist_ok=True)
    if not source.exists():
        return 0

    count = 0
    for path in sorted(item for item in source.rglob("*") if item.is_file()):
        relative = path.relative_to(source)
        target = destination / relative
        target.parent.mkdir(parents=True, exist_ok=True)
        shutil.copy2(path, target)
        count += 1
    return count


def run_wp_cli(php: Path, wp_cli: Path, wp_path: Path, args: list[str]) -> str:
    command = [str(php), str(wp_cli), f"--path={wp_path}", *args]
    result = subprocess.run(command, check=True, capture_output=True, text=True)
    return result.stdout.strip()


def import_blueprints(php: Path, wp_cli: Path, wp_path: Path) -> str:
    return run_wp_cli(php, wp_cli, wp_path, ["course-automation", "import-blueprints"])


def fetch_published_courses(php: Path, wp_cli: Path, wp_path: Path) -> list[PublishedCourse]:
    php_code = r'''
$posts = get_posts(array(
    'post_type' => array('stm-courses', 'stm_lms_courses', 'ca_course_preview'),
    'post_status' => array('draft', 'publish', 'private', 'pending'),
    'posts_per_page' => -1,
    'meta_key' => '_ca_course_id',
    'orderby' => 'title',
    'order' => 'ASC',
));
$items = array();
foreach ($posts as $post) {
    $items[] = array(
        'course_id' => get_post_meta($post->ID, '_ca_course_id', true),
        'post_id' => $post->ID,
        'post_title' => get_the_title($post),
        'post_status' => get_post_status($post),
        'edit_url' => admin_url('post.php?post=' . $post->ID . '&action=edit'),
        'permalink' => get_permalink($post),
    );
}
echo wp_json_encode($items);
'''
    raw = run_wp_cli(php, wp_cli, wp_path, ["eval", php_code])
    payload = json.loads(raw or "[]")
    return [
        PublishedCourse(
            course_id=str(item["course_id"]),
            post_id=int(item["post_id"]),
            post_title=str(item["post_title"]),
            post_status=str(item["post_status"]),
            edit_url=str(item["edit_url"]),
            permalink=str(item["permalink"]),
        )
        for item in payload
        if item.get("course_id")
    ]


def update_tracker_with_published_courses(settings: Settings, courses: list[PublishedCourse]) -> None:
    if not settings.tracker_path.exists():
        return

    by_id = {course.course_id: course for course in courses}
    with settings.tracker_path.open("r", encoding="utf-8-sig", newline="") as handle:
        rows = list(csv.DictReader(handle))

    for row in rows:
        course = by_id.get(row.get("Course ID", ""))
        if not course:
            continue

        row["MasterStudy Added"] = course.post_status
        row["QA Status"] = "ready_for_wordpress_review"
        row["Error/Notes"] = f"MasterStudy draft ID {course.post_id}; edit {course.edit_url}"
        if course.post_status == "publish":
            row["Live URL"] = course.permalink

    with settings.tracker_path.open("w", encoding="utf-8-sig", newline="") as handle:
        writer = csv.DictWriter(handle, fieldnames=TRACKER_COLUMNS)
        writer.writeheader()
        writer.writerows(rows)
