param(
    [string]$OutputDirectory = "backups"
)

$ErrorActionPreference = "Stop"

$projectRoot = Split-Path -Parent $PSScriptRoot
$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$backupRoot = Join-Path $projectRoot $OutputDirectory
$stagingRoot = Join-Path $backupRoot "staging-pre-sso-$timestamp"
$archivePath = Join-Path $backupRoot "sistemujianapp-pre-sso-$timestamp.zip"

$excludePrefixes = @(
    "vendor\",
    "node_modules\",
    "storage\logs\",
    "bootstrap\cache\",
    "backups\"
)

if (-not (Test-Path -LiteralPath $backupRoot)) {
    New-Item -ItemType Directory -Path $backupRoot | Out-Null
}

if (Test-Path -LiteralPath $stagingRoot) {
    Remove-Item -LiteralPath $stagingRoot -Recurse -Force
}

New-Item -ItemType Directory -Path $stagingRoot | Out-Null

Get-ChildItem -LiteralPath $projectRoot -Recurse -Force -File | ForEach-Object {
    $sourcePath = $_.FullName
    $relativePath = $sourcePath.Substring($projectRoot.Length).TrimStart('\')

    foreach ($prefix in $excludePrefixes) {
        if ($relativePath.StartsWith($prefix, [System.StringComparison]::OrdinalIgnoreCase)) {
            return
        }
    }

    $targetPath = Join-Path $stagingRoot $relativePath
    $targetDirectory = Split-Path -Parent $targetPath

    if (-not (Test-Path -LiteralPath $targetDirectory)) {
        New-Item -ItemType Directory -Path $targetDirectory -Force | Out-Null
    }

    Copy-Item -LiteralPath $sourcePath -Destination $targetPath -Force
}

if (Test-Path -LiteralPath $archivePath) {
    Remove-Item -LiteralPath $archivePath -Force
}

Compress-Archive -Path (Join-Path $stagingRoot '*') -DestinationPath $archivePath -CompressionLevel Optimal
Remove-Item -LiteralPath $stagingRoot -Recurse -Force

Write-Output "Backup berhasil dibuat:"
Write-Output $archivePath
