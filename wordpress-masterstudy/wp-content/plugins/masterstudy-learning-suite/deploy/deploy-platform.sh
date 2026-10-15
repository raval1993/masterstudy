#!/usr/bin/env bash
set -euo pipefail
site=/var/www/masterstudy
wp=(php /opt/masterstudy/wordpress-masterstudy/tools/wp-cli.phar --allow-root --path="$site")
stamp=$(date -u +%Y%m%dT%H%M%SZ)
backup=/root/migration/platform-before-$stamp
stage=/root/migration/platform-stage-$stamp
mkdir -m 0700 "$backup" "$stage"
unzip -q /root/migration/masterstudy-platform-0.2.0.zip -d "$stage"
candidate=$stage/platform
pushd "$candidate" >/dev/null
sha256sum -c manifest.sha256
popd >/dev/null
test "$(sha256sum "$site/wp-content/plugins/course-automation-publisher/course-automation-publisher.php" | cut -d' ' -f1)" = 338d3fd54cd3c321582697371b4a2eef4b3589417b00f570bac0d3657d28cdbe
test "$(sha256sum /opt/masterstudy/course-automation/src/course_automation/compliance.py | cut -d' ' -f1)" = a31f90d243a439d9be6da2a1dd5a5c26260f0fbb167ad00cabef797b586b027e
php -l "$candidate/publisher.php"
find "$candidate/masterstudy-learning-suite" -name '*.php' -print0 | xargs -0 -n1 php -l
php "$candidate/masterstudy-learning-suite/tests/test-core.php"
"${wp[@]}" db export "$backup/database.sql" --quiet
cp -a "$site/wp-content/plugins/masterstudy-learning-suite" "$backup/suite"
cp -a "$site/wp-content/plugins/course-automation-publisher/course-automation-publisher.php" "$backup/publisher.php"
cp -a /opt/masterstudy/course-automation/src/course_automation/compliance.py "$backup/compliance.py"
cp -a "$site/wp-config.php" "$backup/wp-config.php"
if systemctl is-active --quiet masterstudy-ai-authoring.service; then echo 'Authoring job is running; wait for its checkpoint before deploying.' >&2; exit 1; fi
systemctl stop masterstudy-ai-authoring.timer
rollback() {
  code=$?
  if test "$code" -ne 0; then
    cp -a "$backup/suite/." "$site/wp-content/plugins/masterstudy-learning-suite/"
    cp -a "$backup/publisher.php" "$site/wp-content/plugins/course-automation-publisher/course-automation-publisher.php"
    cp -a "$backup/compliance.py" /opt/masterstudy/course-automation/src/course_automation/compliance.py
    cp -a "$backup/wp-config.php" "$site/wp-config.php"
    echo "Release failed; code/config restored. Database backup preserved at $backup/database.sql; no enrolments or published media were rolled back." >&2
  fi
  systemctl start masterstudy-ai-authoring.timer
  exit "$code"
}
trap rollback EXIT
install -d -m 0700 -o www-data -g www-data /opt/masterstudy/private-learning
"${wp[@]}" config set MLS_PRIVATE_DIR /opt/masterstudy/private-learning --type=constant --anchor='require_once ABSPATH' --placement=before
for folder in includes assets; do find "$candidate/masterstudy-learning-suite/$folder" -maxdepth 1 -type f -exec install -m 0644 -o www-data -g www-data {} "$site/wp-content/plugins/masterstudy-learning-suite/$folder/" \;; done
install -m 0644 -o www-data -g www-data "$candidate/masterstudy-learning-suite/masterstudy-learning-suite.php" "$site/wp-content/plugins/masterstudy-learning-suite/masterstudy-learning-suite.php"
install -m 0644 -o www-data -g www-data "$candidate/publisher.php" "$site/wp-content/plugins/course-automation-publisher/course-automation-publisher.php"
install -m 0644 "$candidate/compliance.py" /opt/masterstudy/course-automation/src/course_automation/compliance.py
COURSE_AUTOMATION_SRC=/opt/masterstudy/course-automation/src /opt/masterstudy/course-automation/.venv/bin/python "$candidate/test-compliance.py"
"${wp[@]}" eval-file "$candidate/masterstudy-learning-suite/tests/verify-wordpress.php"
"${wp[@]}" eval-file "$candidate/masterstudy-learning-suite/tests/test-wordpress.php"
"${wp[@]}" eval-file "$candidate/masterstudy-learning-suite/tests/test-operations.php"
"${wp[@]}" eval-file "$candidate/masterstudy-learning-suite/tests/test-publisher.php"
"${wp[@]}" eval-file "$candidate/masterstudy-learning-suite/tools/report-status.php"
"${wp[@]}" eval-file "$candidate/masterstudy-learning-suite/tools/audit-catalogue.php" start
printf 'BACKUP=%s\nSTAGE=%s\n' "$backup" "$stage"
