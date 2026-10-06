@echo off
chcp 65001 >nul
cd /d "%~dp0"
where py >nul 2>nul
if %errorlevel%==0 (
    py "%~dp0zip_extract_filetype.py" %*
) else (
    python "%~dp0zip_extract_filetype.py" %*
)
if errorlevel 1 pause
