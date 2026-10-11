@echo off
title TCEK Local Development Server
cls

echo ==============================================================
echo     Trinity College of Engineering & Technology (TCEK)
echo                 Local Development Server
echo ==============================================================
echo.
echo  * Document Root : %~dp0public
echo  * Local URL     : http://localhost:8000
echo  * Admin Panel   : http://localhost:8000/admin/login.php
echo.
echo Starting PHP development server...
echo Press CTRL+C at any time in this window to stop the server.
echo.

:: Launch default web browser after 1 second
start "" cmd /c "timeout /t 1 /nobreak >nul & start http://localhost:8000"

:: Start PHP built-in server with bundled PHP binary
"%~dp0php\php.exe" -S localhost:8000 -t "%~dp0public"

pause
