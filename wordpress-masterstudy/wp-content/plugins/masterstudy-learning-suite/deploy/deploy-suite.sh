#!/usr/bin/env bash
set -euo pipefail
site=/var/www/masterstudy
wp=(php /opt/masterstudy/wordpress-masterstudy/tools/wp-cli.phar --allow-root --path="$site")
stamp=$(date -u +%Y%m%dT%H%M%SZ)
backup=/root/migration/suite-before-$stamp
stage=/root/migration/suite-stage-$stamp
mkdir -m 0700 "$backup" "$stage"
"${wp[@]}" db export "$backup/database.sql" --quiet
if test -d "$site/wp-content/plugins/masterstudy-learning-suite"; then cp -a "$site/wp-content/plugins/masterstudy-learning-suite" "$backup/plugin"; fi
unzip -q /root/migration/masterstudy-learning-suite-0.3.2.zip -d "$stage"
source=$stage/masterstudy-learning-suite
find "$source" -name '*.php' -print0 | xargs -0 -n1 php -l
php "$source/tests/test-core.php"
install -d -m 0700 -o www-data -g www-data /opt/masterstudy/private-learning
"${wp[@]}" config set MLS_PRIVATE_DIR /opt/masterstudy/private-learning --type=constant --anchor='require_once ABSPATH' --placement=before
install -d -m 0755 -o www-data -g www-data "$site/wp-content/plugins/masterstudy-learning-suite"
for folder in includes assets; do
  install -d -m 0755 -o www-data -g www-data "$site/wp-content/plugins/masterstudy-learning-suite/$folder"
  find "$source/$folder" -maxdepth 1 -type f -exec install -m 0644 -o www-data -g www-data {} "$site/wp-content/plugins/masterstudy-learning-suite/$folder/" \;
done
install -m 0644 -o www-data -g www-data "$source/masterstudy-learning-suite.php" "$site/wp-content/plugins/masterstudy-learning-suite/masterstudy-learning-suite.php"
"${wp[@]}" plugin activate masterstudy-learning-suite
if test "${1:-seed}" = seed; then "${wp[@]}" eval-file "$source/tools/seed-banks.php"; fi
"${wp[@]}" eval-file "$source/tests/verify-wordpress.php"
"${wp[@]}" eval-file "$source/tests/test-wordpress.php"
"${wp[@]}" eval-file "$source/tests/test-operations.php"
printf 'BACKUP=%s\n' "$backup"
