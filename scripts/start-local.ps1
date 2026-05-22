# Inicia entorno local: Mailpit (nativo o Docker) + instrucciones backend/frontend
$ErrorActionPreference = "Stop"
$root = Split-Path $PSScriptRoot -Parent
$backend = Join-Path $root "backend"

Write-Host "=== Mailpit ===" -ForegroundColor Cyan
Write-Host "Opción A (sin Docker): en otra terminal ejecuta:"
Write-Host "  .\scripts\start-mailpit.ps1" -ForegroundColor Yellow
Write-Host ""
Write-Host "Opción B (con Docker Desktop):"
Write-Host "  docker compose up -d mailpit" -ForegroundColor Yellow
Write-Host "UI: http://localhost:8025"
Write-Host ""

if (-not (Test-Path (Join-Path $backend ".env"))) {
    Copy-Item (Join-Path $backend ".env.example") (Join-Path $backend ".env")
    Write-Host "Creado backend/.env desde .env.example"
}

Push-Location $backend
if (-not (Test-Path "vendor")) {
    composer install --no-interaction
}
php artisan key:generate --force 2>$null
Pop-Location

Write-Host "=== Tap Terminal (modo local) ===" -ForegroundColor Cyan
Write-Host "MongoDB: mongodb://127.0.0.1:27017/tapterminal"
Write-Host ""
Write-Host "Terminal 1 - Mailpit (si no usas Docker):" -ForegroundColor Yellow
Write-Host "  .\scripts\start-mailpit.ps1"
Write-Host ""
Write-Host "Terminal 2 - Backend:" -ForegroundColor Yellow
Write-Host "  cd backend"
Write-Host "  php artisan serve"
Write-Host ""
Write-Host "Terminal 3 - Frontend:" -ForegroundColor Yellow
Write-Host "  cd frontend"
Write-Host "  npm install"
Write-Host "  npm start"
Write-Host ""
Write-Host "URLs: http://localhost:4200 | http://localhost:8000 | http://localhost:8025"
Write-Host "Login: admin@tapterminal.com / Admin123!"
