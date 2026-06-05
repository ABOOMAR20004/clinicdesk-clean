$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$php = 'C:\xampp\php\php.exe'
$mysql = 'C:\xampp\mysql\bin\mysql.exe'
$mysqld = 'C:\xampp\mysql\bin\mysqld.exe'
$mysqlDefaults = 'C:\xampp\mysql\bin\my.ini'
$databaseFile = Join-Path $root 'config\database.php'
$databaseExample = Join-Path $root 'config\database.example.php'
$sqlFile = Join-Path $root 'database\clinicdesk_db.sql'
$sessionPath = Join-Path $root 'tmp\sessions'

function Fail($message) {
    Write-Host ''
    Write-Host $message -ForegroundColor Red
    Write-Host 'Press any key to close...'
    $null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
    exit 1
}

function Assert-File($path, $label) {
    if (-not (Test-Path -LiteralPath $path)) {
        Fail "$label was not found: $path"
    }
}

function Test-Port($port) {
    return [bool](Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue)
}

function Test-MySql {
    try {
        & $mysql -u root -e 'SELECT 1;' *> $null
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

Assert-File $php 'PHP'
Assert-File $mysql 'MySQL client'
Assert-File $mysqld 'MySQL server'
Assert-File $mysqlDefaults 'MySQL config'
Assert-File $databaseExample 'Database example config'
Assert-File $sqlFile 'Database SQL file'

if (-not (Test-Path -LiteralPath $databaseFile)) {
    Copy-Item -LiteralPath $databaseExample -Destination $databaseFile
}

New-Item -ItemType Directory -Force -Path $sessionPath | Out-Null

if (-not (Test-MySql)) {
    Write-Host 'Starting MySQL...' -ForegroundColor Cyan
    Start-Process -FilePath $mysqld -ArgumentList @("--defaults-file=$mysqlDefaults", '--standalone') -WorkingDirectory (Split-Path -Parent $mysqld) -WindowStyle Hidden | Out-Null

    $ready = $false
    for ($i = 0; $i -lt 30; $i++) {
        Start-Sleep -Seconds 1
        if (Test-MySql) {
            $ready = $true
            break
        }
    }

    if (-not $ready) {
        Fail 'MySQL did not start. Open XAMPP Control Panel and start MySQL, then run this file again.'
    }
}

$hasDatabase = $false
try {
    $result = & $mysql -u root -N -B -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='clinicdesk_db' AND table_name='users';"
    $hasDatabase = [int]($result | Select-Object -First 1) -gt 0
} catch {
    $hasDatabase = $false
}

if (-not $hasDatabase) {
    Write-Host 'Importing clinicdesk_db...' -ForegroundColor Cyan
    Get-Content -LiteralPath $sqlFile -Raw | & $mysql -u root
    if ($LASTEXITCODE -ne 0) {
        Fail 'Database import failed. Check XAMPP MySQL and config/database.php.'
    }
}

$port = 8000
while ((Test-Port $port) -and $port -lt 8010) {
    $port++
}

if ($port -ge 8010 -and (Test-Port $port)) {
    Fail 'Ports 8000-8010 are busy. Stop old PHP servers and try again.'
}

$url = "http://127.0.0.1:$port/index.php"
Write-Host ''
Write-Host 'ClinicDesk is ready.' -ForegroundColor Green
Write-Host "Open: $url" -ForegroundColor Yellow
Write-Host 'Login: admin@clinic.local / Admin@1234'
Write-Host ''
Write-Host 'Keep this window open while using the project. Press Ctrl+C to stop PHP.'
Write-Host ''

& $php -d "session.save_path=$sessionPath" -S "127.0.0.1:$port" -t $root
