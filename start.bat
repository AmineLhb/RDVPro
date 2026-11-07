@echo off
title RDVPro Local Server Setup

:: Check for administrative permissions
net session >nul 2>&1
if %errorLevel% == 0 (
    echo Administrative privileges confirmed.
) else (
    echo Requesting Administrator privileges to check domain mapping in hosts file...
    powershell -Command "Start-Process '%~dpnx0' -Verb RunAs"
    exit /b
)

:: Add domains to hosts file if they don't exist
set HOSTS_PATH=%SystemRoot%\System32\drivers\etc\hosts
set DOMAINS=rdvpro.test admin.rdvpro.test bahja.rdvpro.test mouna.rdvpro.test

echo [1/2] Checking hosts file domain mapping...
for %%D in (%DOMAINS%) do (
    findstr /I /C:"%%D" "%HOSTS_PATH%" >nul
    if errorlevel 1 (
        echo   Adding %%D to hosts...
        echo 127.0.0.1 %%D>> "%HOSTS_PATH%"
    ) else (
        echo   %%D is already mapped.
    )
)
echo.

:: Detect PHP binary
set PHP_BIN=php
where php >nul 2>&1
if errorlevel 1 (
    if exist "C:\MAMP\bin\php\php8.2.14\php.exe" (
        set PHP_BIN="C:\MAMP\bin\php\php8.2.14\php.exe"
    ) else (
        echo [ERROR] PHP is not found on your system PATH or in MAMP folders.
        echo Please ensure PHP is installed and available.
        pause
        exit /b
    )
)

:: Start the artisan server
echo [2/2] Starting local web server...
echo Server running on http://rdvpro.test:8000
echo.
%PHP_BIN% artisan serve --host=0.0.0.0 --port=8000
pause
