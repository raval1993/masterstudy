from __future__ import annotations

import base64
import os
import re
import shutil
import subprocess
import tempfile
import wave
from pathlib import Path
from zipfile import ZipFile

import imageio_ffmpeg
from PIL import Image, ImageDraw, ImageFont, ImageOps

from .settings import Settings


IMAGE_EXTENSIONS = {".png", ".jpg", ".jpeg", ".gif", ".webp", ".bmp"}
VIDEO_SIZE = (1280, 720)
MIN_SLIDE_SECONDS = 3.0
MAX_LESSON_POINT_SLIDES = 4


def enrich_package_with_media(settings: Settings, package: dict[str, object]) -> dict[str, object]:
    assets = extract_docx_images(settings, package)
    assign_assets_to_lessons(package, assets)
    lesson_video_count = render_lesson_videos(settings, package)
    video = render_course_video(settings, package)

    package["source_image_count"] = len(assets)
    package["lesson_video_count"] = lesson_video_count
    package["video_status"] = video["status"]
    package["course_video"] = video
    package.setdefault("next_stage", {})
    if isinstance(package["next_stage"], dict):
        package["next_stage"]["image_extraction"] = "complete" if assets else "no_images_found"
        package["next_stage"]["tts_generation"] = "rendered" if lesson_video_count else "not_rendered"
        package["next_stage"]["lesson_video_rendering"] = "rendered" if lesson_video_count else "not_rendered"
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
                    "title": f"Lesson image {len(assets) + 1}",
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


def render_lesson_videos(settings: Settings, package: dict[str, object]) -> int:
    course_id = clean_filename(str(package.get("course_id", "")).strip())
    if not course_id:
        return 0

    output_dir = settings.generated_videos_dir / course_id
    output_dir.mkdir(parents=True, exist_ok=True)

    rendered_count = 0
    modules = [module for module in package.get("modules", []) if isinstance(module, dict)]
    for module in modules:
        lessons = [lesson for lesson in module.get("lessons", []) if isinstance(lesson, dict)]
        for lesson in lessons:
            lesson_id = clean_filename(str(lesson.get("lesson_id") or lesson.get("title") or "lesson"))
            output_path = output_dir / f"{lesson_id}.mp4"
            slides = build_lesson_video_slides(package, module, lesson)
            result = render_narrated_video(output_path, slides)
            video = lesson.get("video") if isinstance(lesson.get("video"), dict) else {}
            lesson["video"] = video
            video.update(result)
            video["format"] = "mp4"
            video["relative_path"] = f"{course_id}/{output_path.name}" if result.get("status") == "rendered" else ""
            video["source_path"] = str(output_path) if result.get("status") == "rendered" else ""
            video["narration_script"] = "\n\n".join(clean_narration(str(slide.get("narration", ""))) for slide in slides).strip()
            video["scenes"] = [
                {
                    "order": index,
                    "visual": clean_display_text(str(slide.get("title", ""))),
                    "voiceover": clean_narration(str(slide.get("narration", ""))),
                }
                for index, slide in enumerate(slides, start=1)
            ]
            if result.get("status") == "rendered":
                rendered_count += 1

    return rendered_count


def render_course_video(settings: Settings, package: dict[str, object]) -> dict[str, object]:
    course_id = clean_filename(str(package.get("course_id", "")).strip())
    if not course_id:
        return {"status": "skipped"}

    output_dir = settings.generated_videos_dir
    output_dir.mkdir(parents=True, exist_ok=True)
    output_path = output_dir / f"{course_id}.mp4"
    slides = build_course_video_slides(package)
    result = render_narrated_video(output_path, slides)
    if result.get("status") != "rendered":
        return result

    return {
        **result,
        "format": "mp4",
        "relative_path": output_path.name,
        "source_path": str(output_path),
    }


def build_course_video_slides(package: dict[str, object]) -> list[dict[str, object]]:
    course_id = clean_display_text(str(package.get("course_id", "Course")))
    title = clean_display_text(str(package.get("title", "Course")))
    overview = limit_words(clean_display_text(str(package.get("overview", ""))), 65)
    objectives = [clean_display_text(str(item)) for item in package.get("objectives", []) if str(item).strip()]
    modules = [module for module in package.get("modules", []) if isinstance(module, dict)]
    first_images = list(first_assets(package, limit=6))

    slides: list[dict[str, object]] = [
        {
            "eyebrow": course_id,
            "title": title,
            "body": overview,
            "image": first_images[0] if first_images else None,
            "narration": f"Welcome to {title}. {overview}",
        }
    ]

    if objectives:
        slides.append(
            {
                "eyebrow": "Learning Objectives",
                "title": "What You Will Learn",
                "bullets": objectives[:5],
                "image": first_images[1] if len(first_images) > 1 else None,
                "narration": "In this course, you will learn to " + "; ".join(limit_words(item, 16) for item in objectives[:5]) + ".",
            }
        )

    for index, module in enumerate(modules, start=1):
        lessons = [lesson for lesson in module.get("lessons", []) if isinstance(lesson, dict)]
        lesson_titles = [clean_display_text(str(lesson.get("title", ""))) for lesson in lessons[:4]]
        module_title = clean_display_text(str(module.get("title", f"Module {index}")))
        slides.append(
            {
                "eyebrow": f"Module {index}",
                "title": module_title,
                "bullets": lesson_titles,
                "image": first_images[index % len(first_images)] if first_images else None,
                "narration": f"Module {index} focuses on {module_title}. The lessons include " + "; ".join(lesson_titles) + ".",
            }
        )

    slides.append(
        {
            "eyebrow": "Next Steps",
            "title": "Continue Through The Lessons",
            "body": "Watch each lesson video, read the study notes, and complete the checkpoints before moving to the next topic.",
            "image": first_images[-1] if first_images else None,
            "narration": "Continue through the lessons in order. Watch each video, read the study notes, and complete the checkpoints before moving to the next topic.",
        }
    )
    return slides


def build_lesson_video_slides(package: dict[str, object], module: dict[str, object], lesson: dict[str, object]) -> list[dict[str, object]]:
    course_id = clean_display_text(str(package.get("course_id", "")))
    module_title = clean_display_text(str(module.get("title", "Module")))
    title = clean_display_text(str(lesson.get("title", "Lesson")))
    outcome = clean_display_text(str(lesson.get("learning_outcome", "")))
    assets = [asset for asset in lesson.get("assets", []) if isinstance(asset, dict)]
    points = lesson_key_points(lesson)

    slides: list[dict[str, object]] = [
        {
            "eyebrow": f"{course_id} / {module_title}",
            "title": title,
            "body": limit_words(outcome, 45),
            "image": assets[0] if assets else None,
            "narration": f"Welcome to {title}. By the end of this lesson, you should be able to {sentence_lower(outcome)}",
        }
    ]

    if outcome:
        slides.append(
            {
                "eyebrow": "Learning Outcome",
                "title": "What To Focus On",
                "body": limit_words(outcome, 55),
                "image": assets[1] if len(assets) > 1 else None,
                "narration": f"The main focus is this: {outcome}",
            }
        )

    for index, point in enumerate(points[:MAX_LESSON_POINT_SLIDES], start=1):
        slides.append(
            {
                "eyebrow": f"Key Point {index}",
                "title": point["title"],
                "bullets": point["bullets"],
                "image": assets[index % len(assets)] if assets else None,
                "narration": point["narration"],
            }
        )

    recap_actions = [
        "Summarize the main requirement in your own words",
        "Connect it to one real workplace situation",
        "Review the study notes before the knowledge check",
    ]
    slides.append(
        {
            "eyebrow": "Recap",
            "title": "Before You Continue",
            "bullets": recap_actions,
            "image": assets[-1] if assets else None,
            "narration": "Before you continue, summarize the main idea in your own words, connect it to one real workplace situation, and review the study notes before the knowledge check.",
        }
    )

    return slides


def lesson_key_points(lesson: dict[str, object]) -> list[dict[str, object]]:
    points: list[dict[str, object]] = []
    topics = [topic for topic in lesson.get("topics", []) if isinstance(topic, dict)]
    for topic in topics:
        heading = clean_bullet(clean_display_text(str(topic.get("heading", ""))))
        body_items = [clean_display_text(str(item)) for item in topic.get("body", []) if str(item).strip()]
        body = limit_words(first_sentences(" ".join(body_items), 3), 70)
        bullets = split_into_bullets(body, fallback_title=heading)
        if heading and body:
            points.append(
                {
                    "title": limit_words(heading, 12),
                    "bullets": bullets,
                    "narration": f"{heading}. {body}",
                }
            )

    if points:
        return points

    blocks = [block for block in lesson.get("study_material", []) if isinstance(block, dict)]
    paragraphs = [
        clean_display_text(str(block.get("text", "")))
        for block in blocks
        if str(block.get("type", "paragraph")) == "paragraph" and str(block.get("text", "")).strip()
    ]
    for index, paragraph in enumerate(paragraphs[:MAX_LESSON_POINT_SLIDES], start=1):
        body = limit_words(first_sentences(paragraph, 3), 70)
        points.append(
            {
                "title": f"Lesson Point {index}",
                "bullets": split_into_bullets(body, fallback_title=f"Lesson Point {index}"),
                "narration": body,
            }
        )
    return points


def render_narrated_video(output_path: Path, slides: list[dict[str, object]]) -> dict[str, object]:
    if not slides:
        return {"status": "skipped"}

    output_path.parent.mkdir(parents=True, exist_ok=True)
    narration = "\n\n".join(clean_narration(str(slide.get("narration", ""))) for slide in slides).strip()

    try:
        with tempfile.TemporaryDirectory(prefix="course-video-") as temp_name:
            temp_dir = Path(temp_name)
            audio_path = temp_dir / "narration.wav"
            text_path = temp_dir / "narration.txt"
            concat_path = temp_dir / "slides.txt"
            text_path.write_text(narration, encoding="utf-8")
            synthesize_tts_wav(text_path, audio_path)
            duration = wav_duration_seconds(audio_path)
            image_paths = []
            for index, slide in enumerate(slides, start=1):
                slide_path = temp_dir / f"slide_{index:03d}.png"
                render_slide(slide).save(slide_path)
                image_paths.append(slide_path)
            write_concat_file(concat_path, image_paths, allocate_slide_durations(slides, duration))
            run_ffmpeg_concat(concat_path, audio_path, output_path)
    except Exception as exc:  # pragma: no cover - depends on local SAPI and ffmpeg
        return {
            "status": "failed",
            "error": str(exc),
        }

    return {
        "status": "rendered",
        "duration_seconds": round(ffmpeg_duration_seconds(output_path), 1),
        "slide_count": len(slides),
        "has_audio": True,
    }


def synthesize_tts_wav(text_path: Path, audio_path: Path) -> None:
    voice_name = os.environ.get("COURSE_AUTOMATION_TTS_VOICE", "Microsoft Zira").strip()
    try:
        rate = int(os.environ.get("COURSE_AUTOMATION_TTS_RATE", "-1"))
    except ValueError:
        rate = -1
    rate = max(-10, min(10, rate))

    script = f"""
$ErrorActionPreference = 'Stop'
$text = Get-Content -LiteralPath '{ps_quote(str(text_path))}' -Raw -Encoding UTF8
$voice = New-Object -ComObject SAPI.SpVoice
$candidate = $voice.GetVoices() | Where-Object {{ $_.GetDescription() -like '*{ps_quote(voice_name)}*' }} | Select-Object -First 1
if ($candidate) {{ $voice.Voice = $candidate }}
$voice.Rate = {rate}
$stream = New-Object -ComObject SAPI.SpFileStream
$stream.Open('{ps_quote(str(audio_path))}', 3)
$voice.AudioOutputStream = $stream
[void]$voice.Speak($text)
$stream.Close()
"""
    encoded = base64.b64encode(script.encode("utf-16le")).decode("ascii")
    subprocess.run(
        ["powershell", "-NoProfile", "-ExecutionPolicy", "Bypass", "-EncodedCommand", encoded],
        check=True,
        capture_output=True,
        text=True,
    )


def run_ffmpeg_concat(concat_path: Path, audio_path: Path, output_path: Path) -> None:
    ffmpeg = imageio_ffmpeg.get_ffmpeg_exe()
    command = [
        ffmpeg,
        "-y",
        "-f",
        "concat",
        "-safe",
        "0",
        "-i",
        str(concat_path),
        "-i",
        str(audio_path),
        "-c:v",
        "libx264",
        "-preset",
        "veryfast",
        "-crf",
        "24",
        "-c:a",
        "aac",
        "-b:a",
        "96k",
        "-pix_fmt",
        "yuv420p",
        "-shortest",
        str(output_path),
    ]
    subprocess.run(command, check=True, capture_output=True, text=True)


def write_concat_file(path: Path, image_paths: list[Path], durations: list[float]) -> None:
    lines: list[str] = []
    for image_path, duration in zip(image_paths, durations):
        lines.append(f"file '{ffmpeg_concat_path(image_path)}'")
        lines.append(f"duration {duration:.3f}")
    lines.append(f"file '{ffmpeg_concat_path(image_paths[-1])}'")
    path.write_text("\n".join(lines) + "\n", encoding="utf-8")


def allocate_slide_durations(slides: list[dict[str, object]], audio_duration: float) -> list[float]:
    weights = [max(1, count_words(str(slide.get("narration", "")))) for slide in slides]
    total_weight = sum(weights) or len(slides)
    minimum_total = len(slides) * MIN_SLIDE_SECONDS
    duration = max(audio_duration + 0.4, minimum_total)
    return [max(MIN_SLIDE_SECONDS, duration * weight / total_weight) for weight in weights]


def wav_duration_seconds(path: Path) -> float:
    with wave.open(str(path), "rb") as audio:
        frame_rate = audio.getframerate() or 1
        return audio.getnframes() / frame_rate


def ffmpeg_duration_seconds(path: Path) -> float:
    ffmpeg = imageio_ffmpeg.get_ffmpeg_exe()
    probe = subprocess.run(
        [
            ffmpeg,
            "-i",
            str(path),
            "-hide_banner",
        ],
        capture_output=True,
        text=True,
    )
    match = re.search(r"Duration:\s*(\d+):(\d+):(\d+(?:\.\d+)?)", probe.stderr)
    if not match:
        return 0.0
    hours = int(match.group(1))
    minutes = int(match.group(2))
    seconds = float(match.group(3))
    return hours * 3600 + minutes * 60 + seconds


def render_slide(slide: dict[str, object]) -> Image.Image:
    image = Image.new("RGB", VIDEO_SIZE, (246, 248, 251))
    draw = ImageDraw.Draw(image)
    width, height = VIDEO_SIZE

    draw.rectangle((0, 0, width, 86), fill=(20, 41, 67))
    draw.rectangle((0, 86, width, height), fill=(248, 250, 252))

    eyebrow_font = load_font(22, bold=True)
    title_font = load_font(44, bold=True)
    body_font = load_font(25)
    small_font = load_font(22)

    draw.text((64, 28), clean_display_text(str(slide.get("eyebrow", ""))).upper(), fill=(233, 240, 248), font=eyebrow_font)

    image_asset = slide.get("image") if isinstance(slide.get("image"), dict) else None
    text_right = width - 64
    if image_asset:
        media_path = Path(str(image_asset.get("source_path", "")))
        if media_path.exists():
            pasted = safe_open_image(media_path)
            pasted = ImageOps.contain(pasted, (440, 350))
            x = width - pasted.width - 70
            y = 170
            draw.rounded_rectangle((x - 16, y - 16, x + pasted.width + 16, y + pasted.height + 16), radius=8, fill=(255, 255, 255))
            image.paste(pasted, (x, y))
            text_right = x - 54

    y = 150
    for line in wrap_text(clean_display_text(str(slide.get("title", ""))), title_font, text_right - 64, draw)[:3]:
        draw.text((64, y), line, fill=(24, 34, 48), font=title_font)
        y += 56

    y += 14
    bullets = slide.get("bullets")
    if isinstance(bullets, list) and bullets:
        for bullet in bullets[:5]:
            bullet_text = clean_bullet(clean_display_text(str(bullet)))
            if not bullet_text:
                continue
            draw.ellipse((70, y + 11, 84, y + 25), fill=(34, 122, 255))
            for line in wrap_text(bullet_text, small_font, text_right - 112, draw)[:2]:
                draw.text((100, y), line, fill=(47, 60, 78), font=small_font)
                y += 33
            y += 8
    else:
        body = limit_words(clean_display_text(str(slide.get("body", ""))), 70)
        for line in wrap_text(body, body_font, text_right - 64, draw)[:8]:
            draw.text((64, y), line, fill=(47, 60, 78), font=body_font)
            y += 38

    draw.text((64, height - 58), "Pause and take notes before continuing.", fill=(87, 100, 118), font=small_font)
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


def split_into_bullets(text: str, fallback_title: str) -> list[str]:
    sentences = [clean_display_text(item) for item in re.split(r"(?<=[.!?])\s+", text) if clean_display_text(item)]
    bullets = [limit_words(sentence, 18) for sentence in sentences[:4]]
    if len(bullets) >= 2:
        return bullets
    if text:
        chunks = text.split(";")
        bullets = [limit_words(chunk, 18) for chunk in chunks if chunk.strip()][:4]
    return bullets or [fallback_title]


def first_sentences(text: str, limit: int) -> str:
    text = clean_display_text(text)
    sentences = [sentence.strip() for sentence in re.split(r"(?<=[.!?])\s+", text) if sentence.strip()]
    if not sentences:
        return text
    return " ".join(sentences[:limit])


def clean_bullet(text: str) -> str:
    text = re.sub(r"^\d+(?:\.\d+)*\s*[-.\u2013\u2014]?\s*", "", text)
    return re.sub(r"\s+", " ", text).strip()


def clean_display_text(text: str) -> str:
    text = text.replace("\u00a0", " ")
    text = re.sub(r"\s+", " ", text)
    return text.strip()


def clean_narration(text: str) -> str:
    text = clean_display_text(text)
    text = re.sub(r"\b([A-Z]{2,})([A-Z][a-z])", r"\1. \2", text)
    return text


def limit_words(text: str, limit: int) -> str:
    words = clean_display_text(text).split()
    if len(words) <= limit:
        return " ".join(words)
    return " ".join(words[:limit]).rstrip(".,;:") + "."


def sentence_lower(text: str) -> str:
    text = clean_display_text(text).rstrip(".")
    if not text:
        return "describe the main idea from this lesson."
    return text[0].lower() + text[1:] + "."


def count_words(text: str) -> int:
    return len(re.findall(r"\b[\w'-]+\b", text))


def clean_filename(value: str) -> str:
    value = value.strip().replace("\\", "-").replace("/", "-")
    value = re.sub(r"[^A-Za-z0-9._-]+", "-", value)
    value = re.sub(r"-{2,}", "-", value).strip("-._")
    return value or "lesson"


def ps_quote(value: str) -> str:
    return value.replace("'", "''")


def ffmpeg_concat_path(path: Path) -> str:
    return str(path.resolve()).replace("\\", "/").replace("'", "'\\''")
