# Client 37 feature checklist

Recorded on 14 October 2026. The numbering and feature names below come from
`For Sachin Automation for products  i have for the site .docx 1.1.docx`,
paragraphs 6 through 140 in the saved extraction. The same feature list is
repeated in the larger project and product-builder documents.

The first 23 entries are MasterStudy-related features; the next 14 are AI and
training-management features. They are the client's 37 requested capabilities.
The documents then add items 38 through 45, including RTO monitoring and resale.
This list is a saved requirement specification, not a claim of completion.

| No. | Client feature | Current implementation status |
| --- | --- | --- |
| 1 | LMS Statistics & Payouts Engine | Pending custom reporting, commission and payout workflows. |
| 2 | Prerequisites & Course Gatekeeper | Live configurable prerequisite gates. Rules are optional and not silently applied to existing courses. |
| 3 | Online Testing & Advanced Assessment Suite | Live private, timed, randomized attempts and server-side MCQ grading. Ten authored pilot practice assessments are enabled; 4,443 source-reflection banks remain DRAFTS requiring review. This is not an accredited assessment rollout. |
| 4 | Trial Courses & Freemium Funnels | Partial: configurable expiry for free enrolments; payment conversion funnels pending. |
| 5 | Sequential Drip Feeding Engine | Live optional linear progression and enrolment-relative lesson delays, including native access gates. |
| 6 | Multi-Instructor Collaboration Portal | Partial: assigned instructors can author/review banks and grade their courses. Native collaborative course editing pending. |
| 7 | Gamification & XP Reward System | Partial: private XP and badges from unique passed current-release lessons; public leaderboards and a reward ledger pending. |
| 8 | Assignments & Practical Submission Portal | Partial: private written/practical text submissions and instructor decisions. Evidence-file uploads pending. |
| 9 | The Master Gradebook Matrix | Partial: per-course administrator/instructor gradebook and personal results portal; advanced matrix/report pagination pending. |
| 10 | Live Streaming Classroom Integration | Partial: enrolment-protected meeting links and dates; attendance and hosting-service integration pending. |
| 11 | Group Courses & B2B Cohort Portals | Pending cohort management and bulk-seat workflows. |
| 12 | Automated Email Notification Manager | Pending learning-trigger emails and verified mail delivery. |
| 13 | Course Bundling & Dynamic Packaging Engine | Pending purchasable bundles and membership rules. |
| 14 | Google Classrooms & External LMS Bridge | Pending external account/API integration and sync. |
| 15 | Zoom Conference Integration Suite | Partial: manual Zoom meeting links supported. Owner must create a free Zoom account; scheduling API and attendance pending. |
| 16 | SCORM Compliance & Package Player | Pending SCORM package runtime and interoperability tests. |
| 17 | Drag-and-Drop Certificate Builder | Pending verified completion rules and certificate designer. |
| 18 | LMS Custom Forms Editor | Partial: configurable required/optional text fields and private learner answers; drag-and-drop and additional field types pending. |
| 19 | Media File Manager & Secure Storage Vault | Partial: server media and protected transcript access; cloud offload/signed streams pending. |
| 20 | AI Course Creation Lab | Partial: existing source pipeline plus queued private worked-example/practice drafts. Fully generated new course authoring and reviewed content application pending. |
| 21 | WooCommerce Native E-Commerce Bridge | Pending. WooCommerce is not installed on the inspected live site. |
| 22 | Multilanguage & Translation Localization Suite | Pending translated materials and language configuration. |
| 23 | Instructor & Student Public Profiles | Partial: native profiles plus private branded learning hub; public achievement showcases pending. |
| 24 | AI Quiz Generator | Partial: queued free-local-model drafts, structural/source-quote checks and mandatory review. A contradictory first draft was blocked; generated banks are not automatically published. |
| 25 | AI Flashcard Maker | Partial: source-based cards now have release-specific spaced-review scheduling; AI-generated card authoring pending. |
| 26 | AI Training Video Generator | Partial: narrated slide videos, captions and transcripts; talking-avatar generation pending. |
| 27 | AI Course Tutor (RAG-Enabled) | Live local lesson-source retrieval and tutoring with displayed source excerpts. Small model is advisory, not an assessor; course-wide/vector retrieval pending. |
| 28 | AI Automated Grading Engine | Partial: queued private feedback suggestions; instructor approval determines written/practical outcomes. Fully validated grading assistance pending. |
| 29 | AI Rubric Generator & Enforcer | Partial: stored rubrics and AI quiz-rubric drafts; reviewed competency mappings and criterion-level marking pending. |
| 30 | AI Diagram & Educational Image Generator | Partial: existing lesson illustrations; automatic educational diagram generation pending. |
| 31 | Document-to-Course Conversion Engine | Partial: DOCX/source packages become sections, lessons and videos; all proposed formats not covered. |
| 32 | Dynamic SCORM Exporter | Pending an export generator and external LMS validation. |
| 33 | AI Analytics & Predictive Reporting | Partial: bank/attempt totals and own assessment/review data. Predictive models pending; no fabricated risk scores. |
| 34 | Centralized Training & Record-Keeping Vault | Partial: private attempts, instructor decisions, learning events and own JSON record exports. Immutable audit and credential history pending. |
| 35 | Document Revision Management | Partial: release-pinned banks/attempts/cards, stale-bank approval rejection and retired old-release attempts. Revision comparison/rollback workflow pending. |
| 36 | Role-Based Permissions & Data Security Suite | Partial: scoped instructor bank/grade access, enrolled learner REST access, hidden answer keys, private records and HTTPS. Enterprise tenant isolation pending. |
| 37 | Training Receipts & Acknowledgement Logger | Partial: dated lesson acknowledgements; legally reviewed sign-off and immutable receipt workflow pending. |

## Additional requirements

Items 38-45 request: training.gov.au/RTO change monitoring, sellable micro-course
splitting, practical observation and competency grading, automated security
testing, anti-scraping controls, licensing/watermarking, automatic firewall
patching, and the Hulk bulk-import/resale product.

The existing source pipeline and private practice logs cover parts of these
areas. Official unit-change monitoring, accreditation verification, instructor
competency decisions and commercial resale/licensing are not complete.

Some supplied script variants use simulated register content, fictional tutor
matching or unconditional test passes. Those examples do not establish working
RTO compliance, tutoring or resale functionality.

## What is confirmed live

WordPress and MasterStudy, branded homepage/catalogue/course pages, student login,
source-document course publishing, narrated slide videos, captions/transcripts,
scene navigation, review cards, practice checklists, private notes/logs, release-
specific acknowledgements, worksheets, HTTPS and automatic certificate renewal.

Last verified enhancement coverage: 338 published verified courses and 4,443
lessons. The BSBTEC202 pilot has ten authored interactive scenario checks. These
figures do not mean that all 37 features are complete on every course.

## Saved sources

Original document: `C:/Users/user/Downloads/For Sachin Automation for products  i have for the site .docx 1.1.docx`

Ten-document extraction: `C:/cws/LMS/client-documents-extracted.json`

Full review: `C:/cws/LMS/COURSE_ENHANCEMENTS_REVIEW.md`
