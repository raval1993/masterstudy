# MasterStudy Course Enhancements 1.1.0

## Live scope update

The add-on now supports published verified release courses, validating actual
curriculum membership and native access. It adds review cards from authored
sections, a topic checklist, private notes, a learning log, versioned
acknowledgements and a downloadable text worksheet. All ten BSBTEC202 lessons
have authored scenario checks with explanatory feedback.

Learner records use the current WordPress account through nonce-checked REST
requests. MasterStudy completion, enrolments, curriculum and media are unchanged.
Tools > Course enhancements provides release courses and aggregate record counts.

Run `php tests/test-study-tools.php` in addition to the tests below. The native
player asset hook is covered by the regression suite. `tests/verify-live.php`
provides read-only WP-CLI checks and catalogue coverage counts. Requirements and
outstanding work are tracked in `C:/cws/LMS/COURSE_ENHANCEMENTS_REVIEW.md`.

Free standalone WordPress add-on initially developed for BSBTEC202. It does not
require MasterStudy Pro or Pro Plus and does not replace or modify MasterStudy's
lesson player, course records, completion state, or student account system.

## Pilot features

- Scene-aligned English captions from the generated lesson WebVTT transcript.
- An expandable, readable transcript labelled with the lesson's authored scene headings.
- Keyboard-accessible scene buttons that seek the existing MP4 player to the scene start.
- Topic-specific alt text for pilot lesson images that still use the generic `Lesson image` label.
- A private REST endpoint that checks the verified release, course/lesson mapping,
  learner access, transcript location and file size before returning caption data.

The renderer creates one VTT cue per narrated scene. Captions are therefore
scene-aligned, not word-level karaoke captions. Lessons without a verified VTT
remain unchanged; this add-on does not create or invent transcript text.

## Install

Copy this directory to `wp-content/plugins/masterstudy-course-enhancements/`
and activate **MasterStudy Course Enhancements** in WordPress. The expected
server transcript root is `/opt/masterstudy/course-automation/data/business-releases`.
If the server uses another private root, define `MCE_BUSINESS_RELEASES_DIRECTORY`
in `wp-config.php` before WordPress loads. Keep the release files outside the
public web root and ensure the PHP process can read them.

The add-on activates lesson tools for published courses with verified releases.
The currently configured pilot transcript must be present under
`{release-id}/{lesson-id}/transcript.vtt`. No media upload or database migration
is performed by plugin activation.

## Test

Run `php tests/test-vtt.php` and `php -l masterstudy-course-enhancements.php`.
Verify a preview lesson, an enrolled learner, a non-enrolled learner, missing
transcript fallback, captions on/off, keyboard scene navigation, and mobile
layout on a staging copy before production activation.
