# Course Automation

This workspace is the practical automation layer for the MasterStudy LMS project. It starts with the Dropbox `INFORMATION TECHNOLOGY (4)` sample, extracts the raw DOCX training material and embedded images, generates course packages with lessons, scripts, video scene plans, quiz drafts, and MP4 preview videos, then publishes those packages into local Laragon WordPress/MasterStudy.

It follows the client-recommended bridge approach: Python does the heavy parsing, packaging, update monitoring, and LMS preparation outside Coursebox subscription limits. Coursebox can still be used later as an optional downstream tool, but MasterStudy/WordPress remains the publishing target here.

## Current Sample

Downloaded source:

`data/raw/dropbox/information-technology.zip`

Extracted DOCX files:

`data/extracted/information-technology/`

## Run Locally

Use the bundled Codex Python runtime on this machine:

```powershell
$env:PYTHONPATH = "C:\laragon\www\lms-masterstudy\_project\course-automation\src"
$python = "C:\Users\user\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe"
& $python -m course_automation.cli ingest --source "C:\laragon\www\lms-masterstudy\_project\course-automation\data\extracted\information-technology" --category "INFORMATION TECHNOLOGY"
& $python -m course_automation.cli generate-courses
& $python -m course_automation.cli publish-wordpress
& $python -m course_automation.dashboard --host 127.0.0.1 --port 8080
```

Then open:

`http://127.0.0.1:8080`

## Docker

Docker was not detected on this machine during setup, but the project includes Docker files for later use:

```powershell
docker compose up --build
```

## Generated Outputs

The ingestion command writes:

- `data/processed/course_map.json`: source-of-truth course registry mapping course/unit codes to source files
- `data/processed/compliance_update_report.json`: optional update scan report generated from a Training.gov.au CSV/JSON export
- `data/processed/courses/*.json`: structured course records
- `data/processed/blueprints/*.blueprint.json`: source-to-course blueprints
- `data/processed/generated/courses/*.course.json`: generated course packages for WordPress
- `data/processed/generated/bundles/<COURSE_ID>/`: Coursebox/MasterStudy metadata package, production script, quiz bank, and core theory markdown
- `data/processed/generated/scripts/<COURSE_ID>/*.md`: per-lesson narration scripts and video scene plans
- `data/processed/media/source/<COURSE_ID>/*`: images extracted from the original DOCX files
- `data/processed/generated/videos/*.mp4`: generated course preview videos
- `data/processed/markdown/*.md`: readable extracted source text
- `data/processed/course_tracker.csv`: production tracker
- `data/processed/summary.json`: dashboard summary

## Pipeline Stages

The tracker columns follow the intended production flow:

- Source Status
- Extracted
- Content Updated
- Script Generated
- Audio Generated
- Video Generated
- Quiz Added
- MasterStudy Added
- QA Status
- Live URL

`generate-courses` creates reviewable study material, assigns extracted source images to lessons, creates video narration scripts and scene plans, drafts quiz questions, and renders one MP4 preview video per course. TTS/audio narration is still a later enhancement.

## Client-Recommended Scale Workflow

Build or refresh the course registry:

```powershell
$env:PYTHONPATH = "C:\laragon\www\lms-masterstudy\_project\course-automation\src"
$python = "C:\Users\user\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe"
& $python -m course_automation.cli build-registry --source "C:\laragon\www\lms-masterstudy\_project\course-automation\data\extracted\information-technology" --category "INFORMATION TECHNOLOGY"
```

When you have an official Training.gov.au update export as CSV or JSON, scan it against the registry:

```powershell
& $python -m course_automation.cli scan-updates --updates "C:\path\to\training-gov-au-update-export.csv"
```

To keep a Dropbox-synced folder moving automatically while files are added or updated:

```powershell
& $python -m course_automation.cli watch --source "C:\path\to\Dropbox\Courses\INFORMATION TECHNOLOGY" --category "INFORMATION TECHNOLOGY" --publish-wordpress
```

The watcher processes startup content and later file changes. It uses `watchdog` when installed and falls back to polling if that package is unavailable.

Compliance note: the pipeline can flag superseded/non-current units from official exports, but it does not assume that public register material is public domain. Licence, Crown copyright, replacement-unit mapping, and assessment requirements still need review before learner release.

## Push Into Laragon WordPress

After the Laragon WordPress site exists at `C:\laragon\www\lms-masterstudy`, Python can push the generated course packages into MasterStudy:

```powershell
$env:PYTHONPATH = "C:\laragon\www\lms-masterstudy\_project\course-automation\src"
$python = "C:\Users\user\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe"
& $python -m course_automation.cli publish-wordpress
```

This copies the latest generated course packages, extracted images, rendered MP4 files, and blueprint fallback files into WordPress, runs the `Course Automation Publisher` importer, creates or updates MasterStudy courses, published lesson posts, curriculum rows, course preview video metadata, lesson video metadata, and updates `data/processed/course_tracker.csv` with the MasterStudy URLs.
