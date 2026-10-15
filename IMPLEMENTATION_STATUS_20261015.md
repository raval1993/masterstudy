# MasterStudy Enhancement Release Status

Updated 15 October 2026. Architecture remains WordPress + MasterStudy, with free
custom companion plugins. This is a deployed implementation release, not a claim
that all 45 commercial product requirements or all course upgrades are complete.

## Delivered In This Release

- Learning Suite 0.3.2; Course Automation Publisher 0.4.4.
- Visual learner-form editor with text, long text, select, confirmation, date and
  bounded whole-number fields. Server validation, duplicate-key/choice rejection,
  schema conflict checks and private response history are implemented. Assigned
  instructors can review responses; only administrators change the form schema.
  Old responses are not silently applied to changed forms. No role, password or
  arbitrary WordPress profile metadata can be changed through these forms.
- Structured practice rubrics with weighted criterion points, minimums, required
  file evidence and instructor marking. Required evidence is linked to the same
  learner, attempt and release, and its stored file hash is verified. Learners
  see expectations and reviewed criterion scores, but not private marker notes.
- A rubric editor creates a separate assessment version and preserves original
  banks and in-flight attempt snapshots. Practical tasks never auto-pass; a
  reviewer cannot record a pass below the exact weighted threshold, below a
  criterion minimum, or without required evidence. These are formative practice
  workflows, not automatic RTO competency decisions.
- Completion-record eligibility checks the latest enabled bank for each lesson,
  not every historical bank version. Old quiz passes cannot substitute for a new
  practical task, and new learners need not complete an inaccessible old version.
- The BSBTEC202 pilot now has a typed learning-goals form and a source-matched
  workplace-email practical task with three criteria. Other courses were not
  assigned copied generic tasks or bulk-approved source-reflection drafts.
- Private PDF/PNG/JPEG/text evidence uploads, up to five files of 10 MB per attempt.
  Files live outside the public website, have SHA-256 integrity records and are
  downloadable only by the owner or an assigned instructor/administrator.
- Evidence-bearing submissions require human review even with a 100% MCQ score.
  Evidence changes invalidate an already-open marking form. Pending attempts can
  be reopened from the learner hub without creating another attempt.
- Working cohorts: administrator-approved learner rosters, assigned managers,
  seat limits, native MasterStudy enrolment and scoped progress views. Managers
  cannot mark work, read private evidence, or enrol unrelated accounts. Existing
  enrolment start times and progress are not reset.
- Raw WordPress record-editor endpoints are administrator-only; instructors use
  the assigned-course review screens instead of generic post permissions.
- A paginated course gradebook, status filters and CSV export with spreadsheet
  formula protection. The existing marking screen remains available.
- Side-by-side reviewed teaching drafts, manual editing before application,
  saved before/after versions, conflict checks and restore actions. AI feedback
  drafts cannot be appended to course teaching material by this workflow.
- Conservative importer protection for changed content, unknown baselines,
  changed tracked fields/curriculum and removed existing lessons. Conflicts are
  held instead of silently overwriting published work. This is not a complete
  per-field merge editor; legacy baselines still require review.
- Reviewed non-accredited completion records, private print/save-PDF views,
  opaque verification links and revocation. Issuance requires native completion,
  passing enabled current-release banks and an instructor note. These are NOT
  qualifications, Statements of Attainment or automatic competency awards.
- HMAC-linked learning events with an integrity-check CLI tool. Only new sealed
  events are covered; database-administrator access and the absence of off-site
  anchors mean this is tamper-evident, not immutable storage or a legal signature.
- Consent-based learning email outbox and private SMTP configuration support.
  No email delivery is claimed: SMTP is not configured. Delivery success means
  accepted by the transport, not proof of receipt. Failed sends are not retried
  blindly, and existing blocked messages require operator review before replay.
- Course description/Open Graph/Course JSON-LD fallback metadata. The fallback
  yields to Rank Math/Yoast if installed to avoid duplicate plugin output. This
  does not promise ranking, keyword grades or automatically improved copy.
- Apache X-Sendfile media authorization for original publisher MP4 URLs. Native
  enrolment, preview and course gates are checked; byte-range seeking is handled
  by Apache. Legacy publisher courses retain their native enrolment pathway.
  This is access control, not DRM or a guarantee against recording/downloading.
- Compliance-export comparison no longer labels unmatched/ambiguous units
  current. Only explicitly current matched records receive `checked_current`.
- A bounded, checkpointed catalogue quality audit, completed through the existing
  background worker. It does not overwrite content or approve draft assessments.

## Verified Catalogue Findings

| Check | Courses |
| --- | ---: |
| Published course records scanned | 920 |
| At least one review flag | 919 |
| Enabled assessment coverage needs review | 919 |
| Outside the verified release group | 582 |
| At least one lesson without a linked video | 575 |
| Featured-image metadata missing | 85 |
| At least one short lesson (under 500 text characters) | 143 |

Flags overlap and must NOT be added together. A linked video is not proof of a
playable complete video; a text-length check is not an instructional-quality
evaluation. One pilot meets the basic automated checks. The 338 verified-release
courses are internally verified releases, not independently accredited courses.
There are still 4,443 source-reflection draft banks and 11 published pilot bank
versions covering 10 pilot lessons, including the preserved prior quiz version.
This release did not bulk-approve AI outputs or generate the missing
catalogue videos. Those are substantive remaining production/review tasks.

## Complete Requirements Register

"Available" means the scoped workflow works. "Partial" means development and/or
configuration remains. No row is a separately licensed retail product.

| Item | Status after this release | Remaining scope |
| --- | --- | --- |
| 1 Statistics/payouts | Partial | Earnings, commissions and actual disbursements |
| 2 Prerequisites | Available, optional | Approved course-by-course learning paths |
| 3 Online testing | Partial | Review/validate remaining banks and exam mappings |
| 4 Trials | Partial | Commercial lesson-limited trials and conversion |
| 5 Drip/sequence | Available, optional | Approved catalogue-wide schedules |
| 6 Multi-instructor | Partial | Collaborative editing and granular permission splits |
| 7 Gamification | Partial | Durable reward ledger, ranks, opt-in leaderboards/streaks |
| 8 Assignments | Partial, expanded | Rich assignment definitions, observations and AV evidence |
| 9 Gradebook | Partial, expanded | Cross-course matrices and bulk marking |
| 10 Streaming | Partial | Integrated rooms, attendance and chat |
| 11 B2B/groups | Working core cohort workflow | Commercial seats and larger manager reports |
| 12 Email manager | Code available; transport blocked | SMTP account, delivery test, full template editor |
| 13 Bundles | Not implemented | Bundled purchase/entitlement workflows |
| 14 Classroom | Not connected | Owner account/OAuth and roster/grade synchronization |
| 15 Zoom | Manual HTTPS links only | Owner account, approved integration and attendance |
| 16 SCORM player | Not implemented | Proven import/runtime plus interoperability/security tests |
| 17 Certificates | Partial, expanded | Visual designer, QR and approved credential rules |
| 18 Forms | Typed visual-editor core available | Approved profile mappings, conditional logic and broader rollout |
| 19 Media vault | Partial, expanded | S3/R2 offload, expiry links and watermarking |
| 20 AI course lab | Partial, expanded | Reliable reviewed whole-course creation/apply workflows |
| 21 WooCommerce | Not implemented | Product bridge, payments, taxes, refunds/subscriptions |
| 22 Multilanguage | Not implemented | Translation workflows, switching and translated-media QA |
| 23 Profiles | Partial | Opt-in public achievements and instructor showcases |
| 24 AI quizzes | Experimental | Reliable questions/answers and approved catalogue banks |
| 25 AI cards | Partial | Reviewed AI-authored editable cards |
| 26 AI videos | Partial | Missing catalogue videos, avatar/provider integrations |
| 27 RAG tutor | Partial | Whole-course retrieval, stronger model and evaluation |
| 28 AI marking | Advisory only | Validated scoring; human written/practical decisions remain |
| 29 Rubrics | Weighted formative core available | Course/unit-specific authored criteria, qualified review and broader rollout |
| 30 Diagrams/images | Partial | Accurate topic-specific illustrations and 1080p artwork |
| 31 Conversion | Partial | Broader PDF/PPT/media extraction with format QA |
| 32 SCORM export | Not implemented | Proven export and external-LMS testing |
| 33 Analytics | Partial | Engagement/risk reports; no validated predictive model |
| 34 Records vault | Partial, expanded | Retention policies, credential history, off-site anchors |
| 35 Revisions | Partial, expanded | Complete per-field merging and update approvals |
| 36 Security/RBAC | Partial, expanded | Full tenant isolation/provisioning; scoped staging security programme |
| 37 Receipts | Partial | Identity-verified signatures and legally reviewed sign-off |
| 38 TGA monitoring | Comparator corrected; partial | Official update feed, reviewed mappings and scheduled monitoring |
| 39 Micro-course products | Not implemented | SKUs, prices and subject/module entitlements |
| 40 Formal RTO practical assessment | Evidence foundation only | Qualified assessor observations, competency matrices and validation |
| 41 Autonomous security agent | Not deployed | Approved staging scope, review and constrained tooling |
| 42 Anti-scraping | Access controls expanded | Abuse monitoring; no theft-proof promise |
| 43 White-label/DRM | Not implemented | Tenants, licensing, usage metering and user watermarks |
| 44 Auto WAF/patching | Not deployed | Reviewed WAF/update programme with staging and rollback |
| 45 Retail bulk converter | Internal pipeline only | Separate API/product packaging, support and billing |

Additional website/operator requests remain: final approved brand/design, unique
topic artwork, H5P/branching and video hotspots, a comprehensive operator console,
continuous authenticated Dropbox sync, marketing/social exports, PDF/ROI reports,
training materials and dual off-site backups with a restore drill. The core stays
native WordPress; no competing LMS or separate headless platform was introduced.

## Required Owner Inputs

Provide service names and ownership/configuration choices, not secrets in chat:

- Final domain and DNS access.
- SMTP provider and sender identity; credentials belong privately in server config.
- Zoom/Google Classroom owner accounts before OAuth/service authorization.
- Storage account/bucket and retention policy for off-site backups and S3/R2.
- Payment provider, legal seller, currency/prices/tax/refund rules before commerce.
- Qualified content/assessment reviewers and approved unit/evidence mappings.

These inputs do not explain every remaining gap: SCORM, commerce, conditional forms,
translation, analytics, retail licensing and the rest still require development.
They must not be reported to the client as completed or just awaiting a password.

## Operator Access And Verification

- Learner hub: https://212.81.47.151/learning-hub/
- Instructor/admin: WordPress > Learning Suite > Operations.
- Form editor/responses: WordPress > Learning Suite > Learner forms.
- Rubric authoring: WordPress > Learning Suite > Assessment rubrics. Instructor
  criterion marking is in the existing course submission review screen.
- Local canonical project: `C:/laragon/www/lms-masterstudy/_project`.
- Live private evidence: `/opt/masterstudy/private-learning` (never Git/public HTML).
- Live deployment takes a database/code backup before replacement and tests the
  release. Apache media changes have a separate checked rollback path.
- Checks: PHP lint, JavaScript syntax, scoring/privacy/timing/idempotency,
  instructor-review conflicts, cohort rosters/seats/isolation, native enrolment
  preservation, evidence authorization/download, importer conflict safeguards,
  explicit-current compliance matching, trusted HTTPS and actual HTTP 206/403
  media behavior. A disposable learner upload/download matched its source hash.
- Release 0.3.2 additionally checks typed answers, stale schema rejection,
  idempotent saves, private response scoping, weighted marks, required evidence,
  cross-attempt/tampered file rejection, instructor permissions and bank snapshots.
- Desktop and mobile learner evidence screens were visually checked. Temporary
  test accounts, enrolments, attempts and private evidence are removed afterward.

The server's background catalogue scan is complete. It was an audit, not a
background job that will magically finish the remaining features or content.
