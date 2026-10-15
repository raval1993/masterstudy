#!/usr/bin/env bash
set -euo pipefail
site=/var/www/masterstudy
wp=(php /opt/masterstudy/wordpress-masterstudy/tools/wp-cli.phar --allow-root --path="$site")
installed=$("${wp[@]}" plugin get masterstudy-learning-suite --field=version)
case "$installed" in
  0.2.3|0.3.0|0.3.1) ;;
  *) echo "Expected a supported upgrade baseline; found $installed." >&2; exit 1 ;;
esac
stamp=$(date -u +%Y%m%dT%H%M%SZ)
backup=/root/migration/patch-before-$stamp
stage=/root/migration/patch-stage-$stamp
mkdir -m 0700 "$backup" "$stage"
unzip -q /root/migration/masterstudy-learning-suite-0.3.2.zip -d "$stage"
source=$stage/masterstudy-learning-suite
find "$source" -name '*.php' -print0 | xargs -0 -n1 php -l
php "$source/tests/test-core.php"
"${wp[@]}" db export "$backup/database.sql" --quiet
cp -a "$site/wp-content/plugins/masterstudy-learning-suite" "$backup/suite"
if systemctl is-active --quiet masterstudy-ai-authoring.service; then echo 'Wait for the current authoring checkpoint.' >&2; exit 1; fi
systemctl stop masterstudy-ai-authoring.timer
rollback() {
  code=$?
  if test "$code" -ne 0; then cp -a "$backup/suite/." "$site/wp-content/plugins/masterstudy-learning-suite/"; echo "Plugin restored. Private backup: $backup" >&2; fi
  systemctl start masterstudy-ai-authoring.timer
  exit "$code"
}
trap rollback EXIT
for folder in includes assets; do find "$source/$folder" -maxdepth 1 -type f -exec install -m 0644 -o www-data -g www-data {} "$site/wp-content/plugins/masterstudy-learning-suite/$folder/" \;; done
install -m 0644 -o www-data -g www-data "$source/masterstudy-learning-suite.php" "$site/wp-content/plugins/masterstudy-learning-suite/masterstudy-learning-suite.php"
"${wp[@]}" eval-file "$source/tests/verify-wordpress.php"
"${wp[@]}" eval-file "$source/tests/test-wordpress.php"
"${wp[@]}" eval-file "$source/tests/test-operations.php"
"${wp[@]}" eval-file "$source/tests/test-forms-rubrics.php"
"${wp[@]}" eval-file "$source/tests/test-publisher.php"
"${wp[@]}" eval-file "$source/tests/test-media.php"
if test "${1:-}" != no-browser-fixture; then "${wp[@]}" eval-file "$source/tools/test-http-media.php"; fi
printf 'BACKUP=%s\nSTAGE=%s\n' "$backup" "$stage"
