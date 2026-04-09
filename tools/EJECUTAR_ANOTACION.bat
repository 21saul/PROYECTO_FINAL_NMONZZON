@echo off
REM Doble clic o ejecutar desde cmd. Anota todo el PHP del proyecto (excluye Views).
cd /d "%~dp0.."
wsl -d DDEV python3 /home/ddev/www/nmzonzzonstudio/tools/apply_annotation.py /home/ddev/www/nmzonzzonstudio
if errorlevel 1 (
  echo Fallo. Pruebe: wsl -d DDEV python3 /mnt/c/Users/Veintiuno/annotate_php_caps.py /home/ddev/www/nmzonzzonstudio
)
pause
