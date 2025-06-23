[CmdletBinding()]
param(
    [Parameter(Position=0)]
    [string]$param1,

    [Parameter(Position=1)]
    [string]$param2,

    [Parameter(Position=2)]
    [string]$param3,

    [switch]$Force,
    [switch]$Help,
    [switch]$Show
)

function Print-Help {
    $scriptName = "update-version-bump"
    Write-Host @"
`nVERSION BUMP SCRIPT (Скрипт автоматического повышения и понижения версии пакета, с журналом изменений)

VERSION:
  File VERSION in current version (not .etc) example: 1.5.7

HISTORY:
  VERSION_HISTORY.log

MAIN SCRIPT:
  Bump-Version.bat
  Bump-Version.ps1

USAGE:
  $scriptName [command] [options] [version]

COMMANDS:
  major                   Bump major version (e.g. 1.2.3 -> 2.0.0)
  minor                   Bump minor version (e.g. 1.2.3 -> 1.3.0)
  patch                   Bump patch version (e.g. 1.2.3 -> 1.2.4) [default]
  ver <version>           Set specific version (e.g. ver 1.5.7)
  help                    Show this help message
  show                    Show version history

OPTIONS:
  -Force                  Force downgrade without confirmation
  -Help                   Show help
  -Show                   Show version history

EXAMPLES:
  $scriptName minor
  $scriptName ver 1.5.7
  $scriptName ver 1.5.7 -Force
  $scriptName show

NOTE:
  Use '--' to separate parameters in special cases (e.g. when version starts with '-')
"@ -ForegroundColor Cyan
}

try {
    # Установка кодировки для корректной обработки текста
    [Console]::OutputEncoding = [System.Text.Encoding]::UTF8

    # Определение корневой директории скрипта
    $rootPath = Split-Path -Parent $MyInvocation.MyCommand.Path
    if (-not $rootPath) { $rootPath = "." }

    # Путь к файлу истории версий
    $historyPath = Join-Path $rootPath "VERSION_HISTORY.log"
    # Путь к файлу текущей версии
    $versionPath = Join-Path $rootPath "VERSION"

    # Обработка параметра help
    if ($Help -or $param1 -in @('help', '--help', '-?')) {
        Print-Help
        exit 0
    }

    # Обработка параметра show
    if ($Show -or $param1 -eq 'show') {
        Write-Host "`nVERSION HISTORY:`n" -ForegroundColor Green
        if (Test-Path $historyPath) {
            Get-Content $historyPath | ForEach-Object {
                Write-Host $_ -ForegroundColor Cyan
            }
        } else {
            Write-Host "No version history found" -ForegroundColor Yellow
        }
        Write-Host ""
        exit 0
    }

    # Игнорировать одиночный двойной дефис (--)
    if ($param1 -eq "--" -and [string]::IsNullOrEmpty($param2) -and [string]::IsNullOrEmpty($param3)) {
        $param1 = $null
    }

    # Проверка флага Force
    if ($param1 -eq '-Force' -or $param1 -eq '--force' -or $param2 -eq '-Force' -or $param2 -eq '--force' -or $param3 -eq '-Force' -or $param3 -eq '--force') {
        $Force = $true
    }

    # Определение команды и версии
    $command = $null
    $version = $null
    $unknownParams = @()

    # Проверяем первый параметр
    if ($param1 -in @("major", "minor", "patch", "ver")) {
        $command = $param1
        $version = $param2
    }
    # Проверяем второй параметр (если первый был force)
    elseif ($param2 -in @("major", "minor", "patch", "ver")) {
        $command = $param2
        $version = $param3
    }
    # Если есть неизвестные параметры
    elseif (-not [string]::IsNullOrEmpty($param1) -and $param1 -ne "--") {
        $unknownParams += $param1
    }
    elseif (-not [string]::IsNullOrEmpty($param2) -and $param2 -ne "--") {
        $unknownParams += $param2
    }
    elseif (-not [string]::IsNullOrEmpty($param3) -and $param3 -ne "--") {
        $unknownParams += $param3
    }

    # Если обнаружены неизвестные параметры - ошибка
    if ($unknownParams.Count -gt 0) {
        Print-Help
        Write-Host "`nERROR: Unknown parameters: $($unknownParams -join ', ')" -ForegroundColor Red
        Write-Host "Use 'update-version-bump help' for usage information" -ForegroundColor Yellow
        exit 1
    }

    Write-Host "`nVersion history file: $historyPath" -ForegroundColor Cyan

    # Создание файла версии если отсутствует
    if (-not (Test-Path $versionPath)) {
        Set-Content -Path $versionPath -Value "0.0.1"
        Write-Host "Created VERSION file with 0.0.1" -ForegroundColor Green
    }

    # Чтение текущей версии
    $currentVersion = Get-Content -Path $versionPath -Raw
    if ([string]::IsNullOrWhiteSpace($currentVersion)) {
        $currentVersion = "0.0.1"
        Set-Content -Path $versionPath -Value $currentVersion
    }
    $currentVersion = $currentVersion.Trim()
    Write-Host "Current version: $currentVersion" -ForegroundColor Cyan

    # Обработка команды
    $newVersion = $null
    $levelType = $null
    $isDowngrade = $false

    # Если команда не указана - выполняем patch по умолчанию
    if ([string]::IsNullOrEmpty($command)) {
        $command = "patch"
    }

    if ($command -in @("major", "minor", "patch")) {
        $operationType = $command

        # Разбивка версии на компоненты
        $parts = $currentVersion.Split('.')

        # Нормализация до трех компонентов
        while ($parts.Count -lt 3) { $parts += "0" }

        # Увеличение соответствующей части версии
        switch ($operationType) {
            "major" {
                $parts[0] = [int]$parts[0] + 1
                $parts[1] = "0"
                $parts[2] = "0"
            }
            "minor" {
                $parts[1] = [int]$parts[1] + 1
                $parts[2] = "0"
            }
            "patch" {
                $parts[2] = [int]$parts[2] + 1
            }
        }
        $newVersion = $parts -join '.'
        $levelType = $operationType
        Write-Host "Auto-increment $operationType version" -ForegroundColor Blue
    }
    elseif ($command -eq "ver") {
        # Ручная установка версии
        if ([string]::IsNullOrWhiteSpace($version)) {
            Write-Host "`nUSAGE HELP:" -ForegroundColor Yellow
            Write-Host "To set a specific version:"
            Write-Host "  update-version-bump ver <version>" -ForegroundColor Cyan
            Write-Host "Example:"
            Write-Host "  update-version-bump ver 1.5.7" -ForegroundColor Cyan

            $version = Read-Host "`nPlease enter the new version"
        }

        if ([string]::IsNullOrWhiteSpace($version)) {
            Write-Host "`nVersion update canceled" -ForegroundColor Red
            exit 0
        }

        $newVersion = $version.Trim()
        $levelType = "ver"
        Write-Host "Setting custom version: $newVersion" -ForegroundColor Blue
    }

    # Проверка формата версии
    if ($newVersion -notmatch '^\d+(\.\d+)*$') {
        Write-Host "`nERROR: Invalid version format: $newVersion" -ForegroundColor Red
        Write-Host "Version must be numbers separated by dots (e.g. 1.2.3)" -ForegroundColor Yellow
        exit 1
    }

    # Проверка изменения версии
    if ($currentVersion -eq $newVersion) {
        Write-Host "`nVersion unchanged ($currentVersion). No update needed." -ForegroundColor Green
        exit 0
    }

    # Проверка на понижение версии
    $currentParts = $currentVersion.Split('.') | ForEach-Object { [int]$_ }
    $newParts = $newVersion.Split('.') | ForEach-Object { [int]$_ }

    $isDowngrade = $false
    for ($i = 0; $i -lt [Math]::Min($currentParts.Count, $newParts.Count); $i++) {
        if ($newParts[$i] -lt $currentParts[$i]) {
            $isDowngrade = $true
            break
        }
        if ($newParts[$i] -gt $currentParts[$i]) {
            break
        }
    }

    # Обработка понижения версии
    $downgradeMarker = ""
    if ($isDowngrade) {
        # Добавляем маркер для истории
        $downgradeMarker = " down"

        # Проверка доступности интерактивного режима
        $isInteractive = ($Host.UI.RawUI -and
                         [Environment]::UserInteractive -and
                         -not [Console]::IsInputRedirected)

        # Всегда разрешаем downgrade при наличии -Force
        if ($Force) {
            Write-Host "FORCED DOWNGRADE from $currentVersion to $newVersion" -ForegroundColor Magenta
        }
        elseif ($isInteractive) {
            Write-Host "`nWARNING: You are DOWNGRADING version from $currentVersion to $newVersion" -ForegroundColor Yellow
            $confirmation = Read-Host "Do you want to continue? (y/N)"
            if ($confirmation -ne 'y') {
                Write-Host "Version downgrade canceled" -ForegroundColor Red
                exit 0
            }
        }
        else {
            Write-Host "`nERROR: Cannot downgrade version in non-interactive mode" -ForegroundColor Red
            Write-Host "Use '-Force' parameter to override" -ForegroundColor Yellow
            exit 1
        }
    }

    # Формируем тип операции для истории
    $historyOperationType = if ($isDowngrade) {
        "$levelType down"
    } else {
        $levelType
    }

    # Запись в историю изменений
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "$timestamp - $currentVersion -> $newVersion ($historyOperationType)" | Add-Content -Path $historyPath
    Write-Host "Version history updated" -ForegroundColor Cyan

    # Обновление файла версии
    Set-Content -Path $versionPath -Value $newVersion

    if ($isDowngrade) {
        Write-Host "`nNew version (DOWNGRADE): $newVersion" -ForegroundColor Magenta
    } else {
        Write-Host "`nNew version: $newVersion" -ForegroundColor Green
    }

    # Обновление файлов проекта
    $updateScript = Join-Path $rootPath "Update-Version.ps1"
    if (Test-Path $updateScript) {
        & $updateScript
    } else {
        Write-Host "Update-Version.ps1 not found" -ForegroundColor Yellow
    }

    if ($isDowngrade) {
        Write-Host "`nVersion DOWNGRADED successfully!" -ForegroundColor Magenta
    } else {
        Write-Host "`nVersion updated successfully!" -ForegroundColor Green
    }
    exit 0
}
catch {
    Write-Host "`nERROR: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
