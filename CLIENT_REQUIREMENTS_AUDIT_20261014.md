# Client Requirements and Implementation Audit

Reviewed on 14 October 2026. This is a requirements audit, not an implementation
release. No client-supplied script was executed and no live course was changed.

## Conclusion

We are on the right track for the agreed WordPress and MasterStudy architecture.
The LMS, publishing pipeline, narrated lesson media and first student enhancement
release are real and working. We are not yet delivering the client's entire
commercial vision: a fully enriched catalogue, formal RTO assessment system and
45 separately sellable software products with subscriptions and tenant licensing.

The repeated 37-item list describes platform capabilities, not 37 teaching
sections or 37 completed enhancements inside every individual course. The new
resent document expands it to 45 product capabilities, with additional design,
SEO, marketing and operator-dashboard requests.

## Verified Live Numbers

| Measure | Verified result | Meaning |
| --- | ---: | --- |
| Published MasterStudy course records | 920 | Published database records; not a guarantee of full content, videos, assessment or compliance. |
| Published courses with a verified-release flag | 338 | The verified media/release group used by the current enhancement plugins. This flag is internal, not government accreditation. |
| Other published course records | 582 | Outside that verified-release group; need an individual content/media/enhancement audit. Some may have earlier materials or videos. |
| Source-reflection question banks | 4,443 drafts | Prepared, but not student-visible approved assessments. |
| Enabled authored pilot practice banks | 10 | BSBTEC202 formative practice checks; not formal competency awards. |
| Pilot background enrichment job | Private draft awaiting review | Worked-example generation ran; the output has not been applied to student content. |
| AI quiz pilot | Experimental; a generation failed validation | One earlier problematic draft is retained for review and blocked by the current approval checks. No AI-generated bank has been approved. |

These numbers were read from the live WordPress server during this audit.
The client's 850 / 1,250 / 4,500 figures are not a reconciled unique-course
inventory. Do not subtract 920 from those numbers to promise a remaining count.
Folders, duplicate versions, units, subjects and lessons must first be reconciled.

Current deployment: https://212.81.47.151/

Student sign-in: https://212.81.47.151/student-login/

Learning hub: https://212.81.47.151/learning-hub/

## What Is Already Built

- Self-hosted WordPress with MasterStudy and custom free companion plugins.
- Responsive homepage, category catalogue, course overview pages and branded student sign-in.
- Source-document extraction, real MasterStudy course/section/lesson publishing and category organization.
- Selected-category Dropbox intake, CSV inventory handling and incremental transfer batches.
- Source-based narrated slide videos, images, captions, transcripts and scene navigation for the verified release group.
- Business processing with source hashes, duplicate/version review, checkpoints, completed-media reuse and a disk reserve.
- Private student notes, learning logs, practice checklists, worksheets and dated release-specific acknowledgements.
- A private learning hub with assessment results, earned XP/badges, spaced-review scheduling and own-record downloads.
- Server-timed practice attempts, server-side MCQ scoring and instructor review of written responses.
- Optional prerequisite, sequential-learning, drip and free-trial expiry rules.
- Free local lesson-grounded tutoring and a bounded background draft-generation worker.
- HTTPS with certificate renewal and private pre-deployment database/plugin backups.

These capabilities are implemented within their stated scope. They do not mean
that every published record has received full instructional enrichment or that
every client-requested product is complete.

## The Original 37 Capabilities

Status terms: **Live** means a functioning scoped capability; **Partial** means a
useful subset exists; **Experimental** means output is not reliable enough for an
automatic learner rollout; **Missing** means the requested workflow is not built.
None of the rows below is independently packaged and licensed as a retail product.

| No. | Client capability | Status | What exists and what is still required |
| --- | --- | --- | --- |
| 1 | Statistics and payouts | Partial | Bank/attempt totals exist. Earnings, commission splits, payout ledger and disbursements are missing. |
| 2 | Prerequisites | Live | Optional course-progress gates exist. Required learning paths still need course-by-course configuration and approval. |
| 3 | Online testing | Partial | Private timed/randomized attempts, scoring and snapshots work. Ten authored pilot checks are enabled. Most banks are drafts; essay uploads, validated formal exams and competency mappings remain. |
| 4 | Trial courses | Partial | Preview access and optional free-enrolment expiry exist. Paid conversion funnels and lesson-limited commercial trials remain. |
| 5 | Sequential drip | Live | Optional enrolment-relative lesson delays and previous-lesson access checks exist. Catalogue-wide release schedules are not automatically configured. |
| 6 | Multi-instructor | Partial | Assigned instructors can manage their course banks and marking. Full collaborative course editing and finer permission splits remain. |
| 7 | Gamification | Partial | Private XP and milestone badges from unique passed current-release lessons exist. Leaderboards, ranks, streak reminders and a durable reward ledger remain. |
| 8 | Assignments | Partial | Private written/practical text responses and instructor feedback exist. Evidence files, observation uploads and assignment-specific workflows remain. |
| 9 | Gradebook | Partial | Per-course review tables and personal results exist. Full cross-course matrix, filters, pagination, bulk marking and exports remain. |
| 10 | Live streaming | Partial | Enrolled learners can access configured meeting links. Embedded live rooms, verified attendance and integrated chat remain. |
| 11 | Group courses and B2B | Missing | A registered internal group type is not a working cohort portal. Bulk seats, manager dashboards, enrolment and reporting remain. |
| 12 | Email manager | Missing | Mail delivery is not configured; the server has no working sendmail transport. Learning-trigger emails, templates and delivery tests remain. |
| 13 | Course bundles | Missing | Purchasable packages, bundle entitlements, discounts and memberships remain. |
| 14 | Google Classroom | Missing | No account/OAuth connection, roster sync or grade sync exists. |
| 15 | Zoom | Partial | Manual HTTPS meeting links work. Account setup, automatic meeting creation and attendance integration remain. |
| 16 | SCORM player | Missing | No tested SCORM 1.2/2004 import/runtime workflow exists. |
| 17 | Certificate builder | Missing | No drag-and-drop designer, certificate issuance, verification IDs or QR verification exists. |
| 18 | Forms editor | Partial | Configurable required/optional text fields and private responses exist. Visual builder, richer field types and broader enrolment/profile mapping remain. |
| 19 | Media vault | Partial | Server media and private transcript delivery exist. S3/R2 offload, signed access, streaming authorization and watermarking remain. |
| 20 | AI course creation lab | Partial | Source-based course generation and queued private enrichment drafts exist. Full reviewed prompt-to-course authoring, editing and application remain. |
| 21 | WooCommerce | Missing | No live commerce bridge was configured in the verified release. Checkout, payment gateways, taxes, multi-currency and subscriptions remain. |
| 22 | Multilanguage | Missing | Course translations, language switching and translated-media QA remain. |
| 23 | Public profiles | Partial | Native accounts/profiles and a private branded hub exist. Public achievement showcases and enhanced instructor presentation remain. |
| 24 | AI quiz generator | Experimental | Local-model draft jobs and source/structure guards exist. Pilot outputs exposed accuracy problems. Reliable questions, distractors, explanations and reviewer approval remain. |
| 25 | AI flashcards | Partial | Source-based review cards and spaced-review dates work. AI-authored card generation/editing remains. |
| 26 | AI training videos | Partial | Narrated slide videos with captions exist in the verified group. Talking-avatar generation and HeyGen/Synthesia integrations are absent; other catalogue media requires audit. |
| 27 | RAG tutor | Partial | A free local tutor retrieves current-lesson text and displays source excerpts. Whole-course/vector retrieval, evaluation and stronger answer quality remain; hallucination-free performance is not established. |
| 28 | AI grading | Partial | Private AI feedback suggestions can be queued. MCQs use deterministic scoring; written outcomes remain instructor decisions. Validated rubric-based AI scoring remains. |
| 29 | AI rubrics | Partial | Stored rubrics and quiz-rubric drafts exist. Unit-specific reviewed criteria, weighting, evidence mapping and enforcement remain. |
| 30 | Images and diagrams | Partial | Existing illustrations and rendered slides exist. Automatic accurate educational diagrams, technical illustrations and unique topic art remain. |
| 31 | Document conversion | Partial | Existing DOC/DOCX/source-package and CSV pathways work within supported inputs. Full PDF/PPT/media extraction and broader format QA remain. |
| 32 | SCORM export | Missing | No tested exporter or external-LMS interoperability validation exists. |
| 33 | Analytics and prediction | Partial | Basic assessment/review data and totals exist. Engagement funnels, learner-risk modelling, validated predictions and management reports remain. |
| 34 | Training records vault | Partial | Private attempts, notes, decisions, events and learner JSON exports exist. Evidence retention, credential history and tamper-evident audit records remain. |
| 35 | Revision management | Partial | Source/release hashes, snapshots and stale-release rejection exist. Compare/revert UI and conflict-safe preservation of all manual course edits remain. |
| 36 | Permissions and security | Partial | Native access checks, scoped instructor review, private REST responses and HTTPS exist. Corporate manager roles, tenant isolation and full enterprise RBAC remain. |
| 37 | Training receipts | Partial | Dated lesson acknowledgements exist. Identity-verified signatures, immutable receipts and legally reviewed sign-off requirements remain. |

## Eight Additional Product Requirements

| No. | Additional requirement | Status | Remaining work |
| --- | --- | --- | --- |
| 38 | Training.gov.au monitoring | Partial source tooling | A CSV/JSON export comparator exists. Automated official-feed monitoring, release/packaging mappings and reviewed compliance reports are missing. See the comparator issue below. |
| 39 | Sellable micro-courses | Partial parsing only | Courses are parsed into sections/lessons. Individual subject/module SKUs, pricing, prerequisites, enrolment entitlements and commercial bundles are missing. |
| 40 | RTO practical assessment | Partial practice tools | Learning logs/checklists are not qualified observation evidence. Competent / Not Yet Competent matrices, assessor/verifier sign-offs and mapped evidence remain. |
| 41 | Autonomous security testing | Missing | No production AI attack agent is deployed. A scoped staging test programme with approvals, tests and review is required. |
| 42 | Anti-scraping protection | Partial basic access protection | Some private APIs are access/rate limited. Dedicated asset authorization, abuse monitoring and broader anti-download controls remain. No theft-proof guarantee is justified. |
| 43 | White-label licensing and DRM | Missing | Tenant provisioning/isolation, activation keys, entitlement enforcement, usage metering and user-specific media/document watermarks remain. |
| 44 | Automatic WAF and patches | Missing | No AI-driven firewall/hotfix deployment exists. Changes need review, testing, rollback and constrained rollout rather than blind production writes. |
| 45 | Standalone bulk-converter product | Missing as a resale product | The internal MasterStudy pipeline works. A separately supported universal importer, multi-destination service, tenant API, billing and retail deliverables are not built. |

## Other Requests Found in the New Files

| Request | Current status |
| --- | --- |
| New navy/blue visual specification and specified fonts | Responsive branding is built, but the current green/charcoal design is not that exact specification. Review the current demo and agree the final identity first. |
| Unique topic-specific 1080p course artwork | Course-title/module-specific 1280x720 template covers exist in the Business renderer. A complete 1080p topic-art rollout is not done. |
| Updated 2026 training content | Source extraction is not proof of current training-product alignment. Unit/release checking, instructional enrichment and assessor review remain. |
| H5P and branching activities | Existing checks are custom practice tools, not an H5P/adaptive-branching rollout. |
| Interactive video hotspots and embedded questions | Scene seeking and captions work. Hotspots, in-video assessment and digital-reader workflows remain. |
| Full administrative preview/edit/compare/revert console | Basic Python tracker and WordPress review screens exist. The described complete operator console is not deployed. |
| Continuous Dropbox changes flowing through approval into live updates | Intake, hashes, a local-folder watcher and bounded workers exist. Full authenticated sync, safe edit merging, change approval and rollback are not verified end to end. |
| n8n orchestration | No n8n workflow is deployed. Existing Python/systemd workers can remain the automation foundation. |
| SEO titles/descriptions, Course JSON-LD, Open Graph and Rank Math integration | Dedicated client-requested SEO workflow is not implemented/verified. Default WordPress page titles do not constitute the whole SEO suite. |
| Keyword grading, AI SEO rewriting and social post CSV export | Not built. Claimed automatic SEO grades are not proof of search performance. |
| Email/PDF batch reports, ROI reports, operator training and recovery QR | Basic tracker/log/JSON reporting exists. The complete requested handover/reporting pack is missing. |
| GitHub-backed deployment and dual automated backups | The repo has GitHub and old-cPanel remotes; backups exist before live changes. The latest Learning Suite files are untracked, and automated off-site dual backups/restore drills are not established. |
| Custom production domain | Current deployment uses an HTTPS IP. A final client domain has not been configured in this verified work. |
| Headless CMS and independently licensed API products | Current frontend is native WordPress/MasterStudy. A separate headless application and product API are not delivered. |

## Conflicts and Problems in the Supplied Documents

1. **Different LMS targets.** The LearnDash document uses `sfwd-courses`,
   `ldlms/v2`, LearnDash groups and certificate add-ons. These do not describe our
   MasterStudy integration. Adapt the intended workflow; do not install a second
   LMS or paste its API calls into the current project.
2. **Different sites and identities.** The review names the old
   `cloudwebsolutions.agency/masterstudy/` deployment. Other material names
   `answeryes.com.au`, another company and a grant/invitation tool. These are not
   an approved instruction to change our current brand or add unrelated products.
   Show the client the current UltaHost demo before interpreting old-site feedback
   as proof that the current design has failed.
3. **Self-hosted versus external-service claims.** The brief asks for no external
   dependencies while also requiring Dropbox, Zoom, Classroom, S3/R2 and optional
   commercial AI/avatar services. We can self-host the core and make integrations
   optional, but cannot promise those integrations are dependency-free or never
   require maintenance.
4. **Changing course totals.** 850, 1,250 and 4,500 are stated without a unique,
   deduplicated course/subject/lesson inventory. Our 4,443 figure counts lesson
   banks, not 4,443 completed courses.
5. **Factory success is simulated.** The resent factory creates empty plugin
   constructors or a health endpoint, writes a `TEST PASSED` text file and adds
   entries to a local JSON catalogue. That is not 45 working features, a real test
   run, or WooCommerce/Stripe publication. See extracted D1-P607 through D1-P712.
6. **The site enhancer does not finish its claimed publication.** It uses only
   `modules[0]`, creates a course post and a lesson post without linking the native
   curriculum, saves `cover_art.png` without uploading/setting a featured image,
   and accepts an empty video URL. A success message can appear even when lesson
   creation fails. It also reuses a fixed cover filename. See D3-P197 through
   D3-P291. It should not replace our tested publisher.
7. **Unverified accreditation claims.** The enhancer's failure fallback labels
   content and artwork as accredited/current despite missing verification. Those
   labels must not be used to establish currency or accreditation. Official
   assessment guidance requires alignment to the training product and reviewed
   assessment tools, not simply generated copy or a year in a title.
   [ASQA assessment guidance](https://www.asqa.gov.au/for-providers/standards-for-RTOs/practice-guides/quality-area-1-training-and-assessment/assessment-practice-guide).
8. **Security and tenancy are not established by the examples.** A shown API
   variant accepts one shared hard-coded fallback master key and reads jobs by
   job ID without tenant ownership filtering. Another health-endpoint variant is
   public despite a separate variant requiring an administrator. These are not
   production-ready tenant isolation or a safe security subsystem.
9. **SEO promises are not demonstrated.** A keyword-density formula and generated
   A-F score do not establish Google ranking or a formal 2026 SEO standard. Do not
   rewrite course teaching material merely to hit a ratio. Google warns against
   unnatural keyword repetition and low-value scaled content.
   [Google Search spam policies](https://developers.google.com/search/docs/essentials/spam-policies#keyword-stuffing).
10. **Repository links are not release artifacts.** Several download links point
    only to the Gist homepage/starred page or placeholder domains. Statements such
    as "100% complete", "stress-tested" and "handover email sent" are pasted
    conversation claims, not evidence of files deployed or tests run here.

## Gaps Discovered in Our Current Implementation

- **Incorrect default compliance label.** The optional export comparator starts
  unmatched registry units at `up_to_date`. No matching official update is not
  evidence of currency. This should become `not_checked`/`unknown` before the
  comparator is used for client compliance reporting.
  Evidence: `course-automation/src/course_automation/compliance.py:65`.
- **Manual-edit protection is incomplete.** Normal imports protect verified
  releases and preserve manual thumbnails, but authorized/non-protected imports
  still replace generated course and lesson content. A complete per-field
  conflict/override/review policy is not implemented.
  Evidence: publisher guard at line 282; content writes at lines 291 and 1725.
- **Live code is not yet committed.** Git is under
  `C:/laragon/www/lms-masterstudy/_project/.git`, not the WordPress root. Audit
  status shows the new Learning Suite source, deployment copy and status documents
  as untracked. They must be added, committed, pushed and deployment-tested before
  claiming GitHub contains this enhancement release. No Git changes were made
  during this audit.
- **AI quality is the release blocker, not just missing credentials.** The small
  free model can provide lesson support but failed quiz quality/source validation.
  Generated answers, worked examples and rubrics need evaluation and review before
  broad student use. A running worker does not establish correct teaching content.
- **Configured capability is not catalogue-wide completion.** Installing gates,
  forms or an assessment engine does not configure and validate them on all 920
  records. A per-course acceptance checklist is still required.

## Recommended Next Work

1. Agree one requirements register covering the original 37, extra eight and
   website/operator requests. Separate the core LMS release from the later
   independent-software resale product. Keep WordPress/MasterStudy as requested.
2. Show the client the current deployment and approve final brand/design. Commit
   the live custom-plugin release and verify reproducible Git-based deployment.
3. Finish one genuinely enriched pilot: teaching explanations, worked examples,
   topic visuals, reviewed questions and explanations, practical activity,
   references and properly narrated video. Obtain content/assessor sign-off.
4. Reconcile course inventory and roll out the accepted pilot pattern in controlled
   category batches. Audit the 582 other published records instead of assuming
   they have the same verified media/enhancement coverage.
5. Resolve AI draft quality, fix unknown compliance status and implement edit-safe
   updates. Review banks before publishing; do not auto-approve all 4,443 drafts.
6. Add evidence uploads, practical observation, mapped competency criteria,
   assessor decisions and credential/receipt rules before any formal RTO claim.
7. Configure mail and cloud media, then implement checkout, bundles and cohort
   entitlements with the client's service accounts, business settings and budget.
8. Complete certificates, SCORM, SEO, translation and the missing operator workflows.
9. Treat tenant resale/licensing and the universal bulk-converter as a later tested
   commercial release. Run security checks in an authorized staging environment;
   require review before production firewall or code changes.

## Client Update You Can Send

The WordPress/MasterStudy foundation and source-based course publishing are live.
The site currently has 920 published course records, with 338 in the verified
media/enhancement release group. Student login, narrated videos, captions,
transcripts, practice tools and the first learning-hub release are working.
Ten authored pilot practice assessments are enabled; 4,443 additional source-based
question banks are prepared as drafts for review. The remaining work is deeper
course enrichment and validated assessment, followed by commerce, certificates,
SCORM, integrations and the separate software licensing/resale phase. The new
documents have been consolidated into a done/partial/missing checklist; their
sample scripts will be adapted and tested rather than treated as completed products.

## Sources and Evidence

- D1: `C:/Users/user/Downloads/RE SENT AGAIN TO SACHIN MUST USE  & READ .docx`
- D2: `C:/Users/user/Downloads/Course Learn dash and drop box bulk upload codea and links and check list full tools complate for easy intergration .docx`
- D3: `C:/Users/user/Downloads/Site Review & Complete Upgrade Instructions for Sachin  (1).docx`
- Pasted brief: `C:/Users/user/.codex/attachments/0d53a2cc-c23d-44fb-ae9a-ed5d503f7967/Pasted text.txt`
- Saved structured extraction: `C:/cws/LMS/client-requirements-20261014.json`.
  D1/D2/D3 paragraph references refer to this extraction, not invented page numbers.
- Existing release evidence: `C:/cws/LMS/LEARNING_SUITE_RELEASE.md` and
  `C:/cws/LMS/CLIENT_37_FEATURE_CHECKLIST.md`.
- Live read-only WP-CLI checks: published-course count, verified-release/bank/job
  report, and local read-only Git status/remote checks.
- Local source inspection: companion plugins, course publisher, automation
  dashboard, source-batch rendering/checkpoints and optional compliance comparator.

This audit does not certify the courses as accredited, establish resale rights,
or replace review by the client's appropriately qualified training/compliance team.
