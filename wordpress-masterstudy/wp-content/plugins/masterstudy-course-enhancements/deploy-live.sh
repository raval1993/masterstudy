#!/usr/bin/env bash
set -euo pipefail

site=/var/www/masterstudy
archive=/root/migration/masterstudy-course-enhancements-1.1.0.zip
stamp=$(date -u +%Y%m%dT%H%M%SZ)
backup=/root/migration/mce-before-$stamp
staging=/root/migration/mce-stage-$stamp
plugin=$site/wp-content/plugins/masterstudy-course-enhancements
wp=(php /opt/masterstudy/wordpress-masterstudy/tools/wp-cli.phar --allow-root --path="$site")

test -f "$site/wp-config.php"
test -f "$archive"
mkdir -m 0700 "$backup" "$staging"
"${wp[@]}" db export "$backup/database.sql" --quiet
"${wp[@]}" plugin list --format=json > "$backup/plugins.json"
if test -d "$plugin"; then cp -a "$plugin" "$backup/plugin"; fi
unzip -q "$archive" -d "$staging"
source_plugin=$staging/masterstudy-course-enhancements
test -f "$source_plugin/masterstudy-course-enhancements.php"
php -l "$source_plugin/masterstudy-course-enhancements.php"
php -l "$source_plugin/learner-tools.php"
php "$source_plugin/tests/test-vtt.php"
php "$source_plugin/tests/test-study-tools.php"
install -d -m 0755 -o www-data -g www-data "$plugin" "$plugin/assets"
install -d -m 0755 -o www-data -g www-data "$plugin/tools"
install -m 0644 -o www-data -g www-data "$source_plugin/tools/admin-recovery.php" "$plugin/tools/admin-recovery.php"
for name in masterstudy-course-enhancements.php learner-tools.php README.md; do
  install -m 0644 -o www-data -g www-data "$source_plugin/$name" "$plugin/$name"
done
for name in course-tools.css course-tools.js study-tools.js; do
  install -m 0644 -o www-data -g www-data "$source_plugin/assets/$name" "$plugin/assets/$name"
done
"${wp[@]}" plugin activate masterstudy-course-enhancements
"${wp[@]}" eval-file "$source_plugin/tests/verify-live.php" | tee "$backup/verification.json"
"${wp[@]}" eval-file "$source_plugin/tests/verify-study-storage.php"
printf '\nBACKUP=%s\n' "$backup"
