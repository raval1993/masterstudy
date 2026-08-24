param(
    [string] $SitePath = "C:\laragon\www\lms-masterstudy",
    [switch] $SkipMasterStudyLmsDownload
)

$ErrorActionPreference = "Stop"

function Copy-DirectoryContents {
    param(
        [Parameter(Mandatory = $true)][string] $Source,
        [Parameter(Mandatory = $true)][string] $Destination
    )

    if (-not (Test-Path -LiteralPath $Source)) {
        throw "Source folder not found: $Source"
    }

    New-Item -ItemType Directory -Force -Path $Destination | Out-Null
    Copy-Item -Path (Join-Path $Source "*") -Destination $Destination -Recurse -Force
}

$wordpressRoot = Resolve-Path -LiteralPath $SitePath -ErrorAction SilentlyContinue
if (-not $wordpressRoot) {
    throw "WordPress site folder not found: $SitePath. Create it first in Laragon, for example Quick app -> WordPress -> lms-masterstudy."
}

$wordpressRoot = $wordpressRoot.Path
$wpConfig = Join-Path $wordpressRoot "wp-config.php"
$wpContent = Join-Path $wordpressRoot "wp-content"

if (-not (Test-Path -LiteralPath $wpContent)) {
    throw "This does not look like a WordPress folder because wp-content is missing: $wordpressRoot"
}

$workspaceRoot = Resolve-Path -LiteralPath (Join-Path $PSScriptRoot "..\..")
$workspaceRoot = $workspaceRoot.Path
$trackedThemeSource = Join-Path $workspaceRoot "wordpress-masterstudy\wp-content\themes\masterstudy"
$legacyThemeSource = Join-Path $workspaceRoot "masterstudy-4.5.2"
$themeSource = if (Test-Path -LiteralPath $trackedThemeSource) { $trackedThemeSource } else { $legacyThemeSource }
$publisherSource = Join-Path $workspaceRoot "wordpress-masterstudy\wp-content\plugins\course-automation-publisher"
$lmsPluginSource = Join-Path $workspaceRoot "wordpress-masterstudy\wp-content\plugins\masterstudy-lms-learning-management-system"
$blueprintsSource = Join-Path $workspaceRoot "course-automation\data\processed\blueprints"
$coursePackagesSource = Join-Path $workspaceRoot "course-automation\data\processed\generated\courses"
$mediaSource = Join-Path $workspaceRoot "course-automation\data\processed\media\source"
$videosSource = Join-Path $workspaceRoot "course-automation\data\processed\generated\videos"

$themeDestination = Join-Path $wpContent "themes\masterstudy"
$publisherDestination = Join-Path $wpContent "plugins\course-automation-publisher"
$lmsPluginDestination = Join-Path $wpContent "plugins\masterstudy-lms-learning-management-system"
$blueprintsDestination = Join-Path $wpContent "course-automation\blueprints"
$coursePackagesDestination = Join-Path $wpContent "course-automation\courses"
$mediaDestination = Join-Path $wpContent "course-automation\media"
$videosDestination = Join-Path $wpContent "course-automation\videos"

Write-Host "Copying MasterStudy theme..."
Copy-DirectoryContents -Source $themeSource -Destination $themeDestination

Write-Host "Copying Course Automation Publisher plugin..."
Copy-DirectoryContents -Source $publisherSource -Destination $publisherDestination

if (Test-Path -LiteralPath $coursePackagesSource) {
    Write-Host "Copying generated course packages..."
    Copy-DirectoryContents -Source $coursePackagesSource -Destination $coursePackagesDestination
} else {
    Write-Warning "Generated course packages not found yet: $coursePackagesSource"
}

if (Test-Path -LiteralPath $mediaSource) {
    Write-Host "Copying extracted lesson images..."
    Copy-DirectoryContents -Source $mediaSource -Destination $mediaDestination
} else {
    Write-Warning "Extracted media not found yet: $mediaSource"
}

if (Test-Path -LiteralPath $videosSource) {
    Write-Host "Copying generated course videos..."
    Copy-DirectoryContents -Source $videosSource -Destination $videosDestination
} else {
    Write-Warning "Generated videos not found yet: $videosSource"
}

if (Test-Path -LiteralPath $blueprintsSource) {
    Write-Host "Copying generated course blueprints..."
    Copy-DirectoryContents -Source $blueprintsSource -Destination $blueprintsDestination
} else {
    Write-Warning "Generated blueprints not found yet: $blueprintsSource"
}

if (Test-Path -LiteralPath $lmsPluginSource) {
    Write-Host "Copying bundled MasterStudy LMS plugin..."
    Copy-DirectoryContents -Source $lmsPluginSource -Destination $lmsPluginDestination
} elseif (-not $SkipMasterStudyLmsDownload) {
    Write-Host "Downloading latest MasterStudy LMS plugin from WordPress.org..."
    $apiUrl = "https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request%5Bslug%5D=masterstudy-lms-learning-management-system"
    $pluginInfo = (Invoke-WebRequest -Uri $apiUrl -UseBasicParsing).Content | ConvertFrom-Json -AsHashTable
    $downloadLink = $pluginInfo["download_link"]
    if (-not $downloadLink) {
        throw "WordPress.org did not return a download_link for MasterStudy LMS."
    }
    $zipPath = Join-Path $env:TEMP "masterstudy-lms-learning-management-system.zip"
    $extractPath = Join-Path $env:TEMP "masterstudy-lms-learning-management-system"

    if (Test-Path -LiteralPath $extractPath) {
        Remove-Item -LiteralPath $extractPath -Recurse -Force
    }

    Invoke-WebRequest -Uri $downloadLink -OutFile $zipPath
    Expand-Archive -LiteralPath $zipPath -DestinationPath $extractPath -Force

    $pluginFolder = Get-ChildItem -Directory -LiteralPath $extractPath | Select-Object -First 1
    if (-not $pluginFolder) {
        throw "Could not find extracted MasterStudy LMS plugin folder."
    }

    $pluginDestination = Join-Path $wpContent "plugins\$($pluginFolder.Name)"
    Write-Host "Copying MasterStudy LMS plugin..."
    Copy-DirectoryContents -Source $pluginFolder.FullName -Destination $pluginDestination
} else {
    Write-Warning "MasterStudy LMS plugin is not bundled and download was skipped."
}

Write-Host ""
Write-Host "Laragon WordPress target is ready:"
Write-Host "  $wordpressRoot"
Write-Host ""
Write-Host "Next steps in wp-admin:"
Write-Host "  1. Activate the MasterStudy theme."
Write-Host "  2. Activate MasterStudy LMS."
Write-Host "  3. Activate Course Automation Publisher."
Write-Host "  4. Open Course Automation -> Import Course Blueprints."
