# Learning Suite 0.3.2

15 October 2026. WordPress + MasterStudy remain the production architecture.

## Live Scope

- Typed visual learner-form builder and private versioned responses.
- Weighted practice rubrics, criterion minimums and verified evidence mapping.
- Separate assessment versions, preserved attempt snapshots and stale-review locks.
- Completion records check current assessment versions, not superseded banks.
- Learner-visible criteria and reviewed scores, hidden answer keys/marker notes.
- BSBTEC202 pilot: learning-goals form and one workplace-email practical task.

Editors are under WordPress > Learning Suite > Learner forms / Assessment rubrics.
The existing Learning Suite course screen records criterion marks and feedback.
These interfaces apply to assigned verified courses; they do not bulk-author
course-specific criteria or award accredited competency.

## Preserved And Pending

920 published courses remain. There are 11 published pilot bank versions for 10
lessons: the new practical-task version does not delete the previous quiz bank.
The 4,443 source-reflection drafts remain drafts. Existing enrolments, sources,
videos, instructor edits and attempt snapshots are not replaced.

No mass enhancement job was started or claimed. Remaining content production,
question review, SCORM, commerce, translation, advanced gamification/analytics,
tenant/resale features and external account integrations remain tracked in
IMPLEMENTATION_STATUS_20261015.md. SMTP is not configured; email sending is blocked.

## Verification

PHP lint, Node syntax checks and pure validation/scoring tests passed. Live tests
use isolated fixtures for enrolment, schemas, forms, rubric decisions, evidence
ownership/checksums, version conflicts and access controls. Browser verification
covers saved form persistence and desktop/mobile rubric layout. Real HTTPS video
range delivery and guest restrictions are rechecked. Disposable test records are
removed after verification; no customer account is used for test submissions.

Initial private live backup: /root/migration/patch-before-20261015T063025Z.
Final corrective deployment backup: /root/migration/patch-before-20261015T064257Z.
Production private evidence remains outside the website root.
