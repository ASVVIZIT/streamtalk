@echo off
setlocal enabledelayedexpansion

set "command="
set "version="
set "force_flag="

:parse_args
if "%~1" neq "" (
    if "%~1"=="--force" (
        set "force_flag=-Force"
    ) else if "%~1"=="-force" (
        set "force_flag=-Force"
    ) else if "%~1"=="--Force" (
        set "force_flag=-Force"
    ) else if "%~1"=="-Force" (
        set "force_flag=-Force"
    ) else if "%~1"=="force" (
        set "force_flag=-Force"
    ) else if "!command!"=="" (
        set "command=%~1"
    ) else if "!version!"=="" (
        set "version=%~1"
    ) else (
        echo Unexpected parameter: %~1
        exit /b 1
    )
    shift
    goto :parse_args
)

if "!command!"=="ver" (
    powershell -ExecutionPolicy Bypass -File "%~dp0Bump-Version.ps1" !command! !version! !force_flag!
) else (
    powershell -ExecutionPolicy Bypass -File "%~dp0Bump-Version.ps1" !command! !force_flag!
)

if %errorlevel% neq 0 (
    echo Error: Script execution failed
    exit /b %errorlevel%
)
