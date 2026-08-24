$ErrorActionPreference = "Stop"

$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    throw "Docker is not installed or not available on PATH. Install/start Docker Desktop, then run this script again."
}

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
}

docker compose up -d db wordpress phpmyadmin

Write-Host "Waiting for WordPress container..."
Start-Sleep -Seconds 20

docker compose run --rm wpcli sh -lc @'
until wp db check --allow-root >/dev/null 2>&1; do
  echo "Waiting for database..."
  sleep 5
done

if ! wp core is-installed --allow-root >/dev/null 2>&1; then
  wp core install \
    --url="http://localhost:8090" \
    --title="${WORDPRESS_SITE_TITLE}" \
    --admin_user="${WORDPRESS_ADMIN_USER}" \
    --admin_password="${WORDPRESS_ADMIN_PASSWORD}" \
    --admin_email="${WORDPRESS_ADMIN_EMAIL}" \
    --skip-email \
    --allow-root
fi

wp theme activate masterstudy --allow-root
wp plugin install masterstudy-lms-learning-management-system --activate --allow-root
wp plugin activate course-automation-publisher --allow-root
wp rewrite structure '/%postname%/' --allow-root
wp rewrite flush --allow-root
'@

Write-Host "WordPress: http://localhost:8090"
Write-Host "Admin: http://localhost:8090/wp-admin"
Write-Host "phpMyAdmin: http://localhost:8091"
