# Tap Terminal - configuración local en Windows
# Requiere: PHP 8.2 y MongoDB Server ya instalados (winget)

$ErrorActionPreference = "Stop"
$phpDir = (Get-Command php -ErrorAction Stop).Source | Split-Path -Parent
$projectRoot = Split-Path $PSScriptRoot -Parent
$backend = Join-Path $projectRoot "backend"

Write-Host "PHP: $phpDir"

# Extensión MongoDB 2.3 para PHP 8.2 TS x64
$zipUrl = "https://windows.php.net/downloads/pecl/releases/mongodb/2.3.0/php_mongodb-2.3.0-8.2-ts-vs16-x64.zip"
$zipPath = "$env:TEMP\php_mongodb.zip"
Invoke-WebRequest -Uri $zipUrl -OutFile $zipPath -UseBasicParsing
Expand-Archive -Path $zipPath -DestinationPath "$env:TEMP\php_mongodb_ext" -Force
Copy-Item "$env:TEMP\php_mongodb_ext\php_mongodb.dll" "$phpDir\ext\php_mongodb.dll" -Force

if (-not (Test-Path "$phpDir\php.ini")) {
    Copy-Item "$phpDir\php.ini-development" "$phpDir\php.ini"
}

# Composer
$composerBin = "$env:LOCALAPPDATA\Composer"
New-Item -ItemType Directory -Path $composerBin -Force | Out-Null
if (-not (Test-Path "$composerBin\composer.phar")) {
    Invoke-WebRequest -Uri "https://getcomposer.org/download/latest-stable/composer.phar" -OutFile "$composerBin\composer.phar" -UseBasicParsing
}
@'
@echo off
php "%~dp0composer.phar" %*
'@ | Set-Content "$composerBin\composer.bat" -Encoding ASCII

$userPath = [Environment]::GetEnvironmentVariable("Path", "User")
if ($userPath -notlike "*$composerBin*") {
    [Environment]::SetEnvironmentVariable("Path", "$userPath;$composerBin", "User")
}

$env:Path = [Environment]::GetEnvironmentVariable("Path", "Machine") + ";" + [Environment]::GetEnvironmentVariable("Path", "User")

Push-Location $backend
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
composer install --no-interaction
php artisan key:generate --force
php artisan db:seed --force
Pop-Location

Write-Host ""
Write-Host "Listo. Inicia el backend:  cd backend && php artisan serve"
Write-Host "Inicia el frontend: cd frontend && npm install && npm start"
Write-Host "Login: admin@tapterminal.com / Admin123!"
