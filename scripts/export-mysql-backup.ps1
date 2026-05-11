param(
    [string]$OutputDirectory = "backups",
    [string]$Database,
    [string]$DbHost,
    [string]$Port,
    [string]$Username,
    [string]$Password,
    [string]$MysqldumpPath
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Get-EnvMap {
    param([string]$EnvPath)

    $map = @{}

    if (-not (Test-Path -LiteralPath $EnvPath)) {
        return $map
    }

    foreach ($line in Get-Content -LiteralPath $EnvPath) {
        if ([string]::IsNullOrWhiteSpace($line)) {
            continue
        }

        $trimmed = $line.Trim()

        if ($trimmed.StartsWith("#")) {
            continue
        }

        $parts = $trimmed -split "=", 2

        if ($parts.Count -ne 2) {
            continue
        }

        $name = $parts[0].Trim()
        $value = $parts[1].Trim().Trim('"')
        $map[$name] = $value
    }

    return $map
}

function Resolve-MysqldumpPath {
    param([string]$PreferredPath)

    if ($PreferredPath) {
        return (Resolve-Path -LiteralPath $PreferredPath).Path
    }

    $command = Get-Command mysqldump.exe -ErrorAction SilentlyContinue

    if ($command) {
        return $command.Source
    }

    $laragon = Get-ChildItem -Path "C:\laragon\bin\mysql" -Recurse -Filter "mysqldump.exe" -ErrorAction SilentlyContinue |
        Sort-Object FullName -Descending |
        Select-Object -First 1

    if ($laragon) {
        return $laragon.FullName
    }

    throw "mysqldump.exe tidak ditemukan. Isi parameter -MysqldumpPath jika lokasi MySQL berbeda."
}

$projectRoot = Split-Path -Parent $PSScriptRoot
$envMap = Get-EnvMap -EnvPath (Join-Path $projectRoot ".env")

if (-not $Database) { $Database = $envMap["DB_DATABASE"] }
if (-not $DbHost) { $DbHost = $envMap["DB_HOST"] }
if (-not $Port) { $Port = $envMap["DB_PORT"] }
if (-not $Username) { $Username = $envMap["DB_USERNAME"] }
if ($null -eq $Password) { $Password = $envMap["DB_PASSWORD"] }

if (-not $Database) { throw "Nama database belum tersedia. Isi parameter -Database atau set DB_DATABASE di .env." }
if (-not $DbHost) { $DbHost = "127.0.0.1" }
if (-not $Port) { $Port = "3306" }
if (-not $Username) { $Username = "root" }

$mysqldumpExe = Resolve-MysqldumpPath -PreferredPath $MysqldumpPath

$backupRoot = Join-Path $projectRoot $OutputDirectory
if (-not (Test-Path -LiteralPath $backupRoot)) {
    New-Item -ItemType Directory -Path $backupRoot -Force | Out-Null
}

$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$outputPath = Join-Path $backupRoot "$Database-$timestamp.sql"

$arguments = @(
    "--host=$DbHost",
    "--port=$Port",
    "--user=$Username"
)

if (-not [string]::IsNullOrEmpty($Password)) {
    $arguments += "--password=$Password"
}

$arguments += @(
    "--default-character-set=utf8mb4",
    "--single-transaction",
    "--quick",
    "--routines",
    "--triggers",
    "--set-gtid-purged=OFF",
    "--add-drop-table",
    "--result-file=$outputPath",
    $Database
)

& $mysqldumpExe @arguments

if ($LASTEXITCODE -ne 0) {
    throw "Export database gagal dengan exit code $LASTEXITCODE."
}

Write-Output "Backup database berhasil dibuat:"
Write-Output $outputPath
