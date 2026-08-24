from __future__ import annotations

import re
import shutil
from pathlib import Path
from zipfile import ZipFile

import imageio.v2 as imageio
import numpy as np
from PIL import Image, ImageDraw, ImageFont, ImageOps

from .settings import Settings


IMAGE_EXTENSIONS = {".png", ".jpg", ".jpeg", ".gif", ".webp", ".bmp"}
VIDEO_SIZE = (1280, 720)


def enrich_package_with_media(settings: Settings, package: dict[str, object]) -> dict[str, object]:
    assets = extract_docx_images(settings, package)
    assign_assets_to_lessons(package, assets)
    video = render_course_video(settings, package)
    attach_course_video_to_first_lesson(package, video)

    package["source_image_count"] = len(assets)
    package["video_status"] = video["status"]
    package["course_video"] = video
    package.setdefault("next_stage", {})
    if isinstance(package["next_stage"], dict):
        package["next_stage"]["image_extraction"] = "complete" if assets else "no_images_found"
        package["next_stage"]["video_rendering"] = video["status"]
    return package


def extract_docx_images(settings: Settings, package: dict[str, object]) -> list[dict[str, object]]:
    course_id = str(package.get("course_id", "")).strip()
    source_file = Path(str(package.get("source_file", "")))
    if not course_id or not source_file.exists():
        return []

    course_dir = settings.source_media_dir / course_id
    course_dir.mkdir(parents=True, exist_ok=True)

    assets: list[dict[str, object]] = []
    with ZipFile(source_file) as docx:
        media_names = [
            name
            for name in docx.namelist()
            if name.startswith("word/media/") and Path(name).suffix.lower() in IMAGE_EXTENSIONS
        ]

        for index, name in enumerate(media_names, start=1):
            ext = ".jpg" if Path(name).suffix.lower() == ".jpeg" else Path(name).suffix.lower()
            filename = f"image{index:03d}{ext}"
            output_path = course_dir / filename
            with docx.open(name) as source, output_path.open("wb") as destination:
                shutil.copyfileobj(source, destination)

            width = 0
            height = 0
            try:
                with Image.open(output_path) as image:
                    width, height = image.size
            except OSError:
                continue

            if width < 48 or height < 48:
                continue

            assets.append(
                {
                    "type": "image",
                    "asset_id": f"{course_id}-IMG{len(assets) + 1:03d}",
                    "title": f"{course_id} source image {len(assets) + 1}",
                    "relative_path": f"{course_id}/{filename}",
                    "source_path": str(output_path),
                    "width": width,
                    "height": height,
                }
            )

    return assets


def assign_assets_to_lessons(package: dict[str, object], assets: list[dict[str, object]]) -> None:
    lessons = list(iter_lessons(package))
    if not lessons:
        return

    for lesson in lessons:
        lesson["assets"] = []

    if not assets:
        return

    total_lessons = len(lessons)
    total_assets = len(assets)
    for asset_index, asset in enumerate(assets):
        lesson_index = min((asset_index * total_lessons) // total_assets, total_lessons - 1)
        lesson = lessons[lesson_index]
        lesson.setdefault("assets", [])
        lesson["assets"].append(asset)

        video = lesson.get("video") if isinstance(lesson.get("video"), dict) else {}
        scenes = video.get("scenes") if isinstance(video.get("scenes"), list) else []
        if scenes:
            scene_index = min(len(scenes) - 1, max(1, len(lesson["assets"])))
            if isinstance(scenes[scene_index], dict):
                scenes[scene_index]["image_asset"] = asset


def render_course_video(settings: Settings, package: dict[str, object]) -> dict[str, object]:
    course_id = str(package.get("course_id", "")).strip()
    if not course_id:
        return {"status": "skipped"}

    output_dir = settings.generated_videos_dir
    output_dir.mkdir(parents=True, exist_ok=True)
    output_path = output_dir / f"{course_id}.mp4"
    slides = build_course_video_slides(package)

    try:
        with imageio.get_writer(
            output_path,
            fps=1,
            codec="libx264",
            quality=8,
            macro_block_size=16,
            ffmpeg_params=["-pix_fmt", "yuv420p"],
        ) as writer:
            for slide in slides:
                frame = render_slide(slide)
                writer.append_data(np.asarray(frame))
    except Exception as exc:  # pragma: no cover - depends on local ffmpeg binary
        return {
            "status": "failed",
            "error": str(exc),
        }

    return {
        "status": "rendered",
        "format": "mp4",
        "relative_path": output_path.name,
        "source_path": str(output_path),
        "duration_seconds": len(slides),
        "slide_count": len(slides),
    }


def attach_course_video_to_first_lesson(package: dict[str, object], video: dict[str, object]) -> None:
    if video.get("status") != "rendered":
        return

    first_lesson = next(iter_lessons(package), None)
    if not isinstance(first_lesson, dict):
        return

    lesson_video = first_lesson.get("video")
    if not isinstance(lesson_video, dict):
        lesson_video = {}
        first_lesson["video"] = lesson_video

    lesson_video["status"] = "rendered"
    lesson_video["format"] = "mp4"
    lesson_video["relative_path"] = video.get("relative_path", "")
    lesson_video["source_path"] = video.get("source_path", "")


def build_course_video_slides(package: dict[str, object]) -> list[dict[str, object]]:
    course_id = str(package.get("course_id", "Course"))
    title = str(package.get("title", "Course"))
    overview = str(package.get("overview", ""))
    objectives = [str(item) for item in package.get("objectives", []) if str(item).strip()]
    modules = [module for module in package.get("modules", []) if isinstance(module, dict)]
    first_images = list(first_assets(package, limit=4))

    slides: list[dict[str, object]] = [
        {
            "eyebrow": course_id,
            "title": title,
            "body": overview,
            "image": first_images[0] if first_images else None,
        }
    ]

    if objectives:
        slides.append(
            {
                "eyebrow": "Learning Objectives",
                "title": "What learners will be able to do",
                "bullets": objectives[:5],
                "image": first_images[1] if len(first_images) > 1 else None,
            }
        )

    for index, module in enumerate(modules[:4], start=1):
        lessons = [lesson for lesson in module.get("lessons", []) if isinstance(lesson, dict)]
        slides.append(
            {
                "eyebrow": f"Module {index}",
                "title": str(module.get("title", "")),
                "bullets": [str(lesson.get("title", "")) for lesson in lessons[:4]],
                "image": first_images[index % len(first_images)] if first_images else None,
            }
        )

    slides.append(
        {
            "eyebrow": "Course Ready",
            "title": "Structured in MasterStudy LMS",
            "body": "This course has modules, lesson pages, study material, source images, quizzes in draft, and generated video planning.",
            "image": first_images[-1] if first_images else None,
        }
    )
    return slides


def render_slide(slide: dict[str, object]) -> Image.Image:
    image = Image.new("RGB", VIDEO_SIZE, (246, 248, 251))
    draw = ImageDraw.Draw(image)
    width, height = VIDEO_SIZE

    draw.rectangle((0, 0, width, 86), fill=(20, 41, 67))
    draw.rectangle((0, 86, width, height), fill=(248, 250, 252))

    eyebrow_font = load_font(24, bold=True)
    title_font = load_font(46, bold=True)
    body_font = load_font(25)
    small_font = load_font(22)

    draw.text((64, 28), str(slide.get("eyebrow", "")).upper(), fill=(233, 240, 248), font=eyebrow_font)

    image_asset = slide.get("image") if isinstance(slide.get("image"), dict) else None
    text_right = width - 64
    if image_asset:
        media_path = Path(str(image_asset.get("source_path", "")))
        if media_path.exists():
            pasted = safe_open_image(media_path)
            pasted = ImageOps.contain(pasted, (440, 350))
            x = width - pasted.width - 70
            y = 170
            draw.rounded_rectangle((x - 16, y - 16, x + pasted.width + 16, y + pasted.height + 16), radius=18, fill=(255, 255, 255))
            image.paste(pasted, (x, y))
            text_right = x - 54

    y = 150
    for line in wrap_text(str(slide.get("title", "")), title_font, text_right - 64, draw):
        draw.text((64, y), line, fill=(24, 34, 48), font=title_font)
        y += 58

    y += 14
    bullets = slide.get("bullets")
    if isinstance(bullets, list) and bullets:
        for bullet in bullets[:5]:
            bullet_text = clean_bullet(str(bullet))
            if not bullet_text:
                continue
            draw.ellipse((70, y + 11, 84, y + 25), fill=(34, 122, 255))
            for line in wrap_text(bullet_text, small_font, text_right - 112, draw)[:2]:
                draw.text((100, y), line, fill=(47, 60, 78), font=small_font)
                y += 34
            y += 8
    else:
        body = str(slide.get("body", ""))
        for line in wrap_text(body, body_font, text_right - 64, draw)[:8]:
            draw.text((64, y), line, fill=(47, 60, 78), font=body_font)
            y += 38

    draw.text((64, height - 58), "Generated by Course Automation for MasterStudy LMS", fill=(87, 100, 118), font=small_font)
    return image


def iter_lessons(package: dict[str, object]):
    for module in package.get("modules", []):
        if not isinstance(module, dict):
            continue
        for lesson in module.get("lessons", []):
            if isinstance(lesson, dict):
                yield lesson


def first_assets(package: dict[str, object], limit: int):
    count = 0
    for lesson in iter_lessons(package):
        for asset in lesson.get("assets", []):
            if isinstance(asset, dict):
                yield asset
                count += 1
                if count >= limit:
                    return


def safe_open_image(path: Path) -> Image.Image:
    with Image.open(path) as image:
        return image.convert("RGB")


def load_font(size: int, bold: bool = False) -> ImageFont.FreeTypeFont | ImageFont.ImageFont:
    candidates = [
        Path(r"C:\Windows\Fonts\arialbd.ttf" if bold else r"C:\Windows\Fonts\arial.ttf"),
        Path(r"C:\Windows\Fonts\segoeuib.ttf" if bold else r"C:\Windows\Fonts\segoeui.ttf"),
    ]
    for path in candidates:
        if path.exists():
            return ImageFont.truetype(str(path), size=size)
    return ImageFont.load_default()


def wrap_text(text: str, font: ImageFont.ImageFont, max_width: int, draw: ImageDraw.ImageDraw) -> list[str]:
    words = text.split()
    lines: list[str] = []
    current = ""
    for word in words:
        candidate = f"{current} {word}".strip()
        bbox = draw.textbbox((0, 0), candidate, font=font)
        if bbox[2] - bbox[0] <= max_width:
            current = candidate
            continue
        if current:
            lines.append(current)
        current = word
    if current:
        lines.append(current)
    return lines or [""]


def clean_bullet(text: str) -> str:
    text = re.sub(r"^\d+(?:\.\d+)*\s*[-.\u2013\u2014]?\s*", "", text)
    return re.sub(r"\s+", " ", text).strip()
