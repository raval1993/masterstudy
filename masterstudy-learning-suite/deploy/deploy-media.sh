#!/usr/bin/env bash
set -euo pipefail
site=/var/www/masterstudy
wp=(php /opt/masterstudy/wordpress-masterstudy/tools/wp-cli.phar --allow-root --path="$site")
test "$("${wp[@]}" plugin get masterstudy-learning-suite --field=version)" = 0.2.0
test "$(sha256sum "$site/.htaccess" | cut -d' ' -f1)" = d201bd2bd6c85fc02229dd638843593844e96d078c51c04966d8d1502e77d479
test -f /etc/apache2/mods-enabled/xsendfile.load
stamp=$(date -u +%Y%m%dT%H%M%SZ)
backup=/root/migration/media-before-$stamp
stage=/root/migration/media-stage-$stamp
mkdir -m 0700 "$backup" "$stage"
unzip -q /root/migration/masterstudy-learning-suite-0.2.1.zip -d "$stage"
source=$stage/masterstudy-learning-suite
find "$source" -name '*.php' -print0 | xargs -0 -n1 php -l
"${wp[@]}" db export "$backup/database.sql" --quiet
cp -a "$site/.htaccess" "$backup/htaccess"
cp -a "$site/wp-config.php" "$backup/wp-config.php"
cp -a "$site/wp-content/plugins/masterstudy-learning-suite" "$backup/suite"
if systemctl is-active --quiet masterstudy-ai-authoring.service; then echo 'Wait for the current authoring checkpoint.' >&2; exit 1; fi
systemctl stop masterstudy-ai-authoring.timer
rollback() {
  code=$?
  if test "$code" -ne 0; then
    cp -a "$backup/htaccess" "$site/.htaccess"
    cp -a "$backup/wp-config.php" "$site/wp-config.php"
    cp -a "$backup/suite/." "$site/wp-content/plugins/masterstudy-learning-suite/"
    a2disconf masterstudy-media || true
    apache2ctl configtest && systemctl reload apache2
    echo "Media release rolled back. Private backup: $backup" >&2
  fi
  systemctl start masterstudy-ai-authoring.timer
  exit "$code"
}
trap rollback EXIT
for folder in includes assets; do find "$source/$folder" -maxdepth 1 -type f -exec install -m 0644 -o www-data -g www-data {} "$site/wp-content/plugins/masterstudy-learning-suite/$folder/" \;; done
install -m 0644 -o www-data -g www-data "$source/masterstudy-learning-suite.php" "$site/wp-content/plugins/masterstudy-learning-suite/masterstudy-learning-suite.php"
install -m 0644 "$source/deploy/protected-media.conf" /etc/apache2/conf-available/masterstudy-media.conf
"${wp[@]}" config set MLS_PROTECTED_MEDIA true --raw --type=constant --anchor='require_once ABSPATH' --placement=before
install -m 0644 -o www-data -g www-data "$source/deploy/protected-media.htaccess" "$site/.htaccess"
a2enconf masterstudy-media
apache2ctl configtest
systemctl reload apache2
"${wp[@]}" eval-file "$source/tests/test-media.php"
"${wp[@]}" eval-file "$source/tests/test-operations.php"
"${wp[@]}" eval-file "$source/tests/test-wordpress.php"
"${wp[@]}" eval-file "$source/tools/test-http-media.php"
printf 'BACKUP=%s\nSTAGE=%s\n' "$backup" "$stage"
