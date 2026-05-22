# Mailpit sin Docker: descarga el binario de Windows (si falta) y lo ejecuta
$ErrorActionPreference = "Stop"
$root = Split-Path $PSScriptRoot -Parent
$toolsDir = Join-Path $root "tools\mailpit"
$exe = Join-Path $toolsDir "mailpit.exe"
$version = "v1.29.7"
$zipName = "mailpit-windows-amd64.zip"
$zipUrl = "https://github.com/axllent/mailpit/releases/download/$version/$zipName"

if (-not (Test-Path $exe)) {
    Write-Host "Descargando Mailpit $version..." -ForegroundColor Cyan
    New-Item -ItemType Directory -Path $toolsDir -Force | Out-Null
    $zipPath = Join-Path $env:TEMP $zipName
    Invoke-WebRequest -Uri $zipUrl -OutFile $zipPath -UseBasicParsing
    Expand-Archive -Path $zipPath -DestinationPath $toolsDir -Force
    Remove-Item $zipPath -Force
    if (-not (Test-Path $exe)) {
        throw "No se encontró mailpit.exe tras extraer. Revisa tools\mailpit"
    }
    Write-Host "Instalado en $exe" -ForegroundColor Green
}

Write-Host "Mailpit (sin Docker)" -ForegroundColor Cyan
Write-Host "  UI:   http://localhost:8025"
Write-Host "  SMTP: localhost:1025"
Write-Host "Detener: Ctrl+C"
Write-Host ""

& $exe
