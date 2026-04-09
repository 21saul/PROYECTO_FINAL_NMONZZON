# Ejecutar desde PowerShell:  .\tools\subir_github.ps1
# Requiere: Git instalado y credenciales para github.com

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host "Directorio del proyecto: $root"

if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    Write-Error "Git no está en el PATH."
    exit 1
}

if (-not (Test-Path (Join-Path $root ".git"))) {
    git init
}

git add -A
git status

$msg = "Proyecto final nmonzzon Studio"
git commit -m $msg 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "No hay cambios nuevos para commitear o falta configurar user.name/email."
}

git branch -M main 2>$null

$remoteUrl = "https://github.com/21saul/PROYECTO_FINAL_NMONZZON.git"
$hasOrigin = git remote 2>$null | Select-String -Pattern '^origin$' -Quiet
if ($hasOrigin) {
    git remote set-url origin $remoteUrl
} else {
    git remote add origin $remoteUrl
}

Write-Host "Subiendo a origin (main)..."
git push -u origin main
