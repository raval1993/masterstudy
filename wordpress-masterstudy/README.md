# WordPress MasterStudy Target

This folder is where the generated courses will appear after the WordPress side is running.

The local course automation project is here:

`../course-automation`

The tracked MasterStudy theme source is:

`wp-content/themes/masterstudy`

The tracked MasterStudy LMS plugin is:

`wp-content/plugins/masterstudy-lms-learning-management-system`

The generated course packages are:

`../course-automation/data/processed/generated/courses`

The extracted images and generated course videos are:

`../course-automation/data/processed/media/source`

`../course-automation/data/processed/generated/videos`

The source blueprints are kept as fallback:

`../course-automation/data/processed/blueprints`

## Recommended Local Setup: Laragon

Use Laragon instead of Docker if Docker slows down your system.

Laragon is the active local setup for this project.

### Fast Setup From This Workspace

Now that Laragon is installed, this script can create the full local WordPress site:

```powershell
cd C:\laragon\www\lms-masterstudy\_project\wordpress-masterstudy
.\scripts\create-laragon-wordpress.ps1
```

It creates:

- Site folder: `C:\laragon\www\lms-masterstudy`
- Database: `lms_masterstudy`
- Local URL: `http://localhost/lms-masterstudy`
- Admin: `http://localhost/lms-masterstudy/wp-admin`

Default local credentials:

- Username: `admin`
- Password: `admin12345`

### Manual Setup Alternative

#### 1. Create WordPress In Laragon

1. Open Laragon.
2. Start Apache and MySQL.
3. Use `Menu -> Quick app -> WordPress`.
4. Name the site:

`lms-masterstudy`

Laragon normally creates it at:

`C:\laragon\www\lms-masterstudy`

Then finish the normal WordPress browser install.

#### 2. Copy MasterStudy And Importer Into Laragon

After the WordPress site exists, run:

```powershell
cd C:\laragon\www\lms-masterstudy\_project\wordpress-masterstudy
.\scripts\setup-laragon.ps1
```

This copies:

- `wp-content/themes/masterstudy` into the local WordPress theme folder
- `course-automation-publisher` into `wp-content/plugins`
- bundled `masterstudy-lms-learning-management-system` into `wp-content/plugins`
- generated course packages into `wp-content/course-automation/courses`, when available
- extracted images into `wp-content/course-automation/media`, when available
- generated videos into `wp-content/course-automation/videos`, when available
- generated blueprints into `wp-content/course-automation/blueprints`

#### 3. Activate In WordPress Admin

In wp-admin:

1. Activate the `MasterStudy` theme.
2. Activate the `MasterStudy LMS` plugin.
3. Activate the `Course Automation Publisher` plugin.
4. Go to `Course Automation`.
5. Click `Import Course Blueprints`.

The four Information Technology courses will then appear as draft MasterStudy course records. The importer also creates `stm-lessons` posts and writes MasterStudy curriculum section/material rows so the lessons are attached to each course.

## Import With WP-CLI Optional

If Laragon has WP-CLI available:

```powershell
cd C:\laragon\www\lms-masterstudy\_project\wordpress-masterstudy
.\scripts\import-blueprints-laragon.ps1
```

If WP-CLI is not available, use the admin screen button.

## Optional Docker Path

Docker files remain in this folder only as an alternate setup. Once Docker Desktop is installed and running, use:

```powershell
cd C:\laragon\www\lms-masterstudy\_project\wordpress-masterstudy
Copy-Item .env.example .env
.\scripts\setup-wordpress.ps1
```

## Important

The current importer creates structured course records, MasterStudy lesson posts, curriculum metadata, lesson image blocks, course preview video URLs, and first-lesson video URLs. Audio narration, certificates, pricing rules, and publish approval remain later stages.
