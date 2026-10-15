# Learning Suite release - 14 October 2026

WordPress and MasterStudy remain the architecture. The custom free plugin is
`masterstudy-learning-suite`; it supplements, not replaces, the native LMS.

## Live entry points

- Learner portal: https://212.81.47.151/learning-hub/
- Student sign-in: https://212.81.47.151/student-login/
- Administration: WordPress > Learning Suite. Administrators configure a course;
  assigned instructors can review its banks and submissions.
- Lesson tools appear for signed-in enrolled learners on verified release lessons.

## Assessment rollout

The initial seeding created 4,443 draft banks, one per verified teaching lesson.
These contain source-based reflection tasks, with ten existing authored pilot
MCQs added to the BSBTEC202 banks. Drafts do not appear as student assessments
until reviewed and approved. They do not establish RTO competency or accreditation.
Ten existing authored BSBTEC202 pilot checks are also enabled separately as
timed formative practice assessments. They are not AI banks or competency awards.

The assessment engine stores private server-timed attempts and bank snapshots.
MCQs are scored on the server. Written/practical responses await instructor
review. Duplicate submissions, expired attempts and other-user access are blocked.
Existing enrolments, curricula, media and native completion remain unchanged.

## Free local AI

Ollama with `qwen3:0.6b` runs only at `127.0.0.1:11435`. Cloud mode is disabled.
Service: `masterstudy-local-ai.service`. It has a 1,600 MB memory ceiling, a
150% CPU ceiling, one model/worker and a short idle unload period.

The tutor retrieves text from the current lesson and displays its source excerpts.
It is advisory and can be wrong. The first live pilot answer matched the lesson's
multifactor-authentication guidance.

Quiz/worked-example/rubric/feedback generation is queued through WordPress cron.
The `masterstudy-ai-authoring.timer` invokes a bounded worker every two minutes,
processing at most three due jobs per run even without website traffic. This is
a server queue worker, not a recurring Codex status-check notification.
Generated work remains private/draft. Jobs record failures instead of publishing
broken results. A first quiz draft contradicted its own answer key. Subsequent
guardrails require exactly two positive MCQs with correct options copied from
actual source text; approval repeats these checks. This still needs human review
of the question, distractors, explanations and rubric. Do not mass-publish AI banks.
The subsequent pilot quiz was rejected for failing the source-quote check. The
small local model is not yet reliable for automated quiz authoring; this feature
remains experimental rather than a completed catalogue-wide question generator.

No paid AI provider, external AI key, Zoom account, Google Classroom connection,
cloud-storage credential or payment account has been configured.
The server has no working sendmail transport; outgoing learning notifications
and password-reset email delivery require a privately configured mail service.

## Zoom

Zoom Basic supports free meetings up to 40 minutes / 100 participants:
https://www.zoom.com/en/products/virtual-meetings/features/free-video-conferencing/

The owner creates the free account, schedules a meeting and supplies its HTTPS
meeting URL. Learning Suite > course > Live classroom accepts that URL and a
date. This is manual linking, not automatic scheduling or verified attendance.

## Verification and limits

PHP syntax and standalone tests passed. Live WordPress regressions covered hidden
answer keys, attempt privacy, start/submit idempotency, forged-score rejection,
JSON quote preservation, timing expiry, drip denial and guest REST access.
Regression attempts/accounts/events are removed after tests.
Final live verification: 338 published verified-release courses, 4,443 source
reflection draft banks, ten approved pilot practice banks, zero remaining test
learners, and both local-AI and authoring-timer services active. The pilot
background worked-example job completed as a private draft awaiting review.
Student-browser tests covered native sign-in, a scored practice attempt, earned
XP, private result history, record download and spaced-review persistence.
Desktop/mobile portal checks and mobile navigation passed.

Private database/plugin backups are taken before deployment in `/root/migration`.
CLI tests and deployment helpers are not installed in the public plugin directory.

This is an incremental release, NOT completion of all 37 features. Payouts,
cohorts, email delivery, purchasable bundles, Classroom syncing, SCORM import/export,
certificate designer, WooCommerce, translation, avatar videos, predictive analytics
and SaaS/resale still need implementation or external setup. See the updated
`CLIENT_37_FEATURE_CHECKLIST.md` for item-by-item scope.
