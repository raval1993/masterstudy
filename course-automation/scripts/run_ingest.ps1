$ErrorActionPreference = "Stop"

$root = Split-Path -Parent $PSScriptRoot
$env:PYTHONPATH = Join-Path $root "src"
$python = "C:\Users\user\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe"
$source = Join-Path $root "data\extracted\information-technology"

& $python -m course_automation.cli ingest --source $source --category "INFORMATION TECHNOLOGY"
