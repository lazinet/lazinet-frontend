@echo off
echo ================================================
echo           LAZINET Website Local Server
echo ================================================
echo.
echo Starting local development server...
echo Website will be available at: http://localhost:8000
echo.
echo Press Ctrl+C to stop the server
echo ================================================
echo.

cd /d "%~dp0public"

REM Try different methods to serve the website
if exist "C:\Program Files\nodejs\node.exe" (
    echo Using Node.js server...
    npx serve -l 8000
) else if exist "python.exe" (
    echo Using Python server...
    python -m http.server 8000
) else if exist "C:\Program Files\Git\usr\bin\python.exe" (
    echo Using Git Python server...
    "C:\Program Files\Git\usr\bin\python.exe" -m http.server 8000
) else if exist "php.exe" (
    echo Using PHP server...
    php -S localhost:8000
) else (
    echo.
    echo ERROR: No suitable web server found!
    echo Please install one of the following:
    echo - Node.js ^(recommended^): https://nodejs.org/
    echo - Python: https://python.org/
    echo - PHP: https://php.net/
    echo.
    echo Or open index.html directly in your browser.
    pause
)

pause