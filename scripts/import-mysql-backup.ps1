param(
    [Parameter(Mandatory = $true)]
    [string]$SqlFile,
    [string]$Database,
    [string]$DbHost,
    [string]$Port,
    [string]$Username,
    [string]$Password,
    [string]$MysqlPath,
    [switch]$CreateDatabase
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

function Resolve-MysqlPath {
    param([string]$PreferredPath)

    if ($PreferredPath) {
        return (Resolve-Path -LiteralPath $PreferredPath).Path
    }

    $command = Get-Command mysql.exe -ErrorAction SilentlyContinue

    if ($command) {
        return $command.Source
    }

    $laragon = Get-ChildItem -Path "C:\laragon\bin\mysql" -Recurse -Filter "mysql.exe" -ErrorAction SilentlyContinue |
        Sort-Object FullName -Descending |
        Select-Object -First 1

    if ($laragon) {
        return $laragon.FullName
    }

    throw "mysql.exe tidak ditemukan. Isi parameter -MysqlPath jika lokasi MySQL berbeda."
}

$projectRoot = Split-Path -Parent $PSScriptRoot
$envMap = Get-EnvMap -EnvPath (Join-Path $projectRoot ".env")

if (-not $Database) { $Database = $envMap["DB_DATABASE"] }
if (-not $DbHost) { $DbHost = $envMap["DB_HOST"] }
if (-not $Port) { $Port = $envMap["DB_PORT"] }
if (-not $Username) { $Username = $envMap["DB_USERNAME"] }
if ($null -eq $Password) { $Password = $envMap["DB_PASSWORD"] }

if (-not $Database) { throw "Nama database tujuan belum tersedia. Isi parameter -Database atau set DB_DATABASE di .env." }
if (-not $DbHost) { $DbHost = "127.0.0.1" }
if (-not $Port) { $Port = "3306" }
if (-not $Username) { $Username = "root" }

$sqlPath = (Resolve-Path -LiteralPath $SqlFile).Path
$mysqlExe = Resolve-MysqlPath -PreferredPath $MysqlPath

$baseArguments = @(
    "--host=$DbHost",
    "--port=$Port",
    "--user=$Username"
)

if (-not [string]::IsNullOrEmpty($Password)) {
    $baseArguments += "--password=$Password"
}

if ($CreateDatabase) {
    $createDatabaseSql = "CREATE DATABASE IF NOT EXISTS ``$Database`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    & $mysqlExe @baseArguments "-e" $createDatabaseSql

    if ($LASTEXITCODE -ne 0) {
        throw "Pembuatan database tujuan gagal dengan exit code $LASTEXITCODE."
    }
}

$importArguments = $baseArguments + "--database=$Database"
[System.IO.File]::ReadAllText($sqlPath) | & $mysqlExe @importArguments

if ($LASTEXITCODE -ne 0) {
    throw "Import database gagal dengan exit code $LASTEXITCODE."
}

Write-Output "Import database berhasil:"
Write-Output $sqlPath
