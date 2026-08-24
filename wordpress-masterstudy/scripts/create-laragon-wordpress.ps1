param(
    [string] $SiteName = "lms-masterstudy",
    [string] $SiteUrl = "http://localhost/lms-masterstudy",
    [string] $DbName = "lms_masterstudy",
    [string] $DbUser = "root",
    [string] $DbPassword = "",
    [string] $AdminUser = "admin",
    [string] $AdminPassword = "admin12345",
    [string] $AdminEmail = "admin@example.com",
    [string] $Title = "AI Course LMS"
)

$ErrorActionPreference = "Stop"

function Find-FirstFile {
    param(
        [Parameter(Mandatory = $true)][string] $Root,
        [Parameter(Mandatory = $true)][string] $Pattern
    )
    $item = Get-ChildItem -Recurse -File -LiteralPath $Root -Filter $Pattern -ErrorAction SilentlyContinue | Select-Object -First 1
    if (-not $item) {
        throw "Could not find $Pattern under $Root"
    }
    return $item.FullName
}

$laragonRoot = "C:\laragon"
$wwwRoot = Join-Path $laragonRoot "www"
$sitePath = Join-Path $wwwRoot $SiteName

if (-not (Test-Path -LiteralPath $laragonRoot)) {
    throw "Laragon not found at $laragonRoot"
}

$php = Find-FirstFile -Root (Join-Path $laragonRoot "bin\php") -Pattern "php.exe"
$mysql = Find-FirstFile -Root (Join-Path $laragonRoot "bin\mysql") -Pattern "mysql.exe"
$workspaceRoot = Resolve-Path -LiteralPath (Join-Path $PSScriptRoot "..\..")
$workspaceRoot = $workspaceRoot.Path
$wpCli = Join-Path $workspaceRoot "wordpress-masterstudy\tools\wp-cli.phar"

New-Item -ItemType Directory -Force -Path $sitePath | Out-Null
New-Item -ItemType Directory -Force -Path (Split-Path -Parent $wpCli) | Out-Null

if (-not (Test-Path -LiteralPath (Join-Path $sitePath "wp-settings.php"))) {
    Write-Host "Downloading WordPress..."
    $wpZip = Join-Path $env:TEMP "wordpress-latest.zip"
    $wpExtract = Join-Path $env:TEMP "wordpress-latest"
    if (Test-Path -LiteralPath $wpExtract) {
        Remove-Item -LiteralPath $wpExtract -Recurse -Force
    }
    Invoke-WebRequest -Uri "https://wordpress.org/latest.zip" -OutFile $wpZip
    Expand-Archive -LiteralPath $wpZip -DestinationPath $wpExtract -Force
    Copy-Item -Path (Join-Path $wpExtract "wordpress\*") -Destination $sitePath -Recurse -Force
}

if (-not (Test-Path -LiteralPath $wpCli)) {
    Write-Host "Downloading WP-CLI..."
    Invoke-WebRequest -Uri "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar" -OutFile $wpCli
}

Write-Host "Creating database $DbName..."
if ($DbPassword) {
    & $mysql -u $DbUser "-p$DbPassword" -e "CREATE DATABASE IF NOT EXISTS ``$DbName`` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
} else {
    & $mysql -u $DbUser -e "CREATE DATABASE IF NOT EXISTS ``$DbName`` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
}

if (-not (Test-Path -LiteralPath (Join-Path $sitePath "wp-config.php"))) {
    Write-Host "Creating wp-config.php..."
    & $php $wpCli --path="$sitePath" config create `
        --dbname="$DbName" `
        --dbuser="$DbUser" `
        --dbpass="$DbPassword" `
        --dbhost="localhost" `
        --skip-check
}

Write-Host "Installing WordPress core if needed..."
& $php $wpCli --path="$sitePath" core is-installed 2>$null
if ($LASTEXITCODE -ne 0) {
    & $php $wpCli --path="$sitePath" core install `
        --url="$SiteUrl" `
        --title="$Title" `
        --admin_user="$AdminUser" `
        --admin_password="$AdminPassword" `
        --admin_email="$AdminEmail" `
        --skip-email
}

Write-Host "Copying project theme, plugin, LMS plugin, and blueprints..."
& (Join-Path $workspaceRoot "wordpress-masterstudy\scripts\setup-laragon.ps1") -SitePath $sitePath

Write-Host "Activating theme and plugins..."
& $php $wpCli --path="$sitePath" theme activate masterstudy
& $php $wpCli --path="$sitePath" plugin activate masterstudy-lms-learning-management-system
& $php $wpCli --path="$sitePath" plugin activate course-automation-publisher
& $php $wpCli --path="$sitePath" rewrite structure "/%postname%/"
& $php $wpCli --path="$sitePath" rewrite flush

Write-Host "Importing course blueprints..."
& $php $wpCli --path="$sitePath" course-automation import-blueprints

Write-Host ""
Write-Host "WordPress is ready:"
Write-Host "  Site:  $SiteUrl"
Write-Host "  Admin: $SiteUrl/wp-admin"
Write-Host "  User:  $AdminUser"
Write-Host "  Pass:  $AdminPassword"
