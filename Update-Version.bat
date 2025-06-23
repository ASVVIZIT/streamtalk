@echo off
powershell -ExecutionPolicy Bypass -File "%~dp0Update-Version.ps1"
if %errorlevel% neq 0 (
    echo Error: Script execution failed
    exit /b %errorlevel%
)
