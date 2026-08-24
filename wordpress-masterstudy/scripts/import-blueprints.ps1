$ErrorActionPreference = "Stop"

$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    throw "Docker is not installed or not available on PATH. Install/start Docker Desktop, then run this script again."
}

docker compose run --rm wpcli wp course-automation import-blueprints --allow-root
