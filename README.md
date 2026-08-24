# MasterStudy Course Automation

This repository contains the custom automation layer for building MasterStudy LMS courses from source training files.

The current local milestone processes the Dropbox `INFORMATION TECHNOLOGY` sample, extracts DOCX course content and embedded images, generates structured lessons, scripts, quiz drafts, and MP4 preview videos, then publishes the result into a Laragon WordPress site using a custom MasterStudy importer plugin.

## What Is In Git

- `course-automation/`: Python ingestion, course generation, media extraction, video rendering, dashboard, and WordPress publishing scripts.
- `wordpress-masterstudy/wp-content/plugins/course-automation-publisher/`: custom WordPress plugin that imports generated packages into MasterStudy courses, lessons, curriculum rows, images, and video metadata.
- `wordpress-masterstudy/scripts/`: Laragon and optional Docker setup helpers.

Generated Dropbox data, extracted course files, images, videos, local WordPress uploads, downloaded MasterStudy plugin/theme copies, and machine-specific files are intentionally excluded from Git.

## Local Pipeline

From this machine, after Laragon is running and the local WordPress site exists at `http://localhost/lms-masterstudy/`:

```powershell
cd C:\cws\LMS\course-automation
.\scripts\run_full_pipeline.ps1
```

That command ingests the source files, generates course packages, extracts images, renders course preview videos, publishes into WordPress/MasterStudy, and updates the tracker.

## Current Sample Result

The first batch imports four Information Technology courses into MasterStudy with structured sections, lessons, images, curriculum records, course preview videos, and first-lesson video links.

Next planned production steps are TTS audio generation, per-lesson video rendering, queue/retry handling for large batches, and optional S3-compatible or video CDN storage for generated media.

## Deployment Direction

For cPanel or another server, keep WordPress/MasterStudy on the server and store generated videos either on server disk for MVP or later in object/video storage such as S3-compatible storage, Cloudflare R2, Bunny Stream, or DigitalOcean Spaces.
