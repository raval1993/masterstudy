param(
    [string] $SitePath = "C:\laragon\www\lms-masterstudy"
)

$ErrorActionPreference = "Stop"

$wp = Get-Command wp -ErrorAction SilentlyContinue

if ($wp) {
    & wp --path="$SitePath" course-automation import-blueprints
    exit $LASTEXITCODE
}

Write-Host "WP-CLI was not found on PATH."
Write-Host "Import manually from WordPress admin:"
Write-Host "  1. Open your Laragon WordPress admin."
Write-Host "  2. Go to Course Automation."
Write-Host "  3. Click Import Course Blueprints."
