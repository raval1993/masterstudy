#!/usr/bin/env bash
set -euo pipefail
source=$(cd "$(dirname "$0")/.." && pwd)
site=/var/www/masterstudy
wp=(php /opt/masterstudy/wordpress-masterstudy/tools/wp-cli.phar --allow-root --path="$site")
test "$("${wp[@]}" plugin get masterstudy-learning-suite --field=version)" = 0.3.2
for folder in includes assets; do
  while IFS= read -r -d '' file; do
    cmp "$file" "$site/wp-content/plugins/masterstudy-learning-suite/$folder/$(basename "$file")"
  done < <(find "$source/$folder" -maxdepth 1 -type f -print0)
done
cmp "$source/masterstudy-learning-suite.php" "$site/wp-content/plugins/masterstudy-learning-suite/masterstudy-learning-suite.php"
php "$source/tests/test-core.php"
"${wp[@]}" eval-file "$source/tests/test-forms-rubrics.php"
"${wp[@]}" eval-file "$source/tools/test-http-media.php"
"${wp[@]}" eval-file "$source/tools/finalize-release.php"
"${wp[@]}" eval-file "$source/tools/report-status.php"
"${wp[@]}" post list --post_type=stm-courses --post_status=publish --format=count
"${wp[@]}" user list --search='mls-*' --fields=ID,user_login --format=json
systemctl is-active apache2 masterstudy-ai-authoring.timer
