try {
    [Console]::OutputEncoding = [System.Text.Encoding]::UTF8

    $rootPath = Split-Path -Parent $MyInvocation.MyCommand.Path
    if (-not $rootPath) { $rootPath = "." }

    # Путь к файлу истории версий
    $historyPath = Join-Path $rootPath "VERSION_HISTORY.log"
    # Путь к файлу текущей версии
    $versionPath = Join-Path $rootPath "VERSION"

    # Проверка конфигурационного файла
    $configPath = Join-Path $rootPath "version-config.json"
    if (-not (Test-Path $configPath)) {
        throw "Configuration file version-config.json not found"
    }

    $config = Get-Content -Path $configPath -Raw | ConvertFrom-Json

    # Проверка и создание файла версии
    $versionSource = $config.version_source
    $versionPath = Join-Path $rootPath $versionSource
    if (-not (Test-Path $versionPath)) {
        Set-Content -Path $versionPath -Value "0.0.1"
        Write-Host "INFO: Created $versionSource file" -ForegroundColor Cyan
    }

    # Чтение версии
    $newVersion = (Get-Content -Path $versionPath -Raw).Trim()
    Write-Host "INFO: Updating to version: $newVersion" -ForegroundColor Cyan

    # Определяем, был ли скрипт вызван вручную или автоматически
    $isManualCall = $MyInvocation.InvocationName -like "*Update-Version*"

    # Получаем последнюю версию из истории (если есть)
    $lastVersion = $null
    if (Test-Path $historyPath) {
        $lastEntry = Get-Content $historyPath -Tail 1
        if ($lastEntry -match '-> (\d+\.\d+\.\d+) \(') {
            $lastVersion = $matches[1]
        } elseif ($lastEntry -match 'Manual update to (\d+\.\d+\.\d+)') {
            $lastVersion = $matches[1]
        }
    }

    # Проверяем, изменилась ли версия с момента последнего обновления
    $versionChanged = $true
    if ($lastVersion -and $lastVersion -eq $newVersion) {
        $versionChanged = $false
        # Выводим сообщение только при ручном вызове
        if ($isManualCall) {
            Write-Host "INFO: Version $newVersion already updated. Skipping history log." -ForegroundColor Cyan
        }
    }

    # Запись в историю ТОЛЬКО для ручных вызовов и если версия изменилась
    if ($isManualCall -and $versionChanged) {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        "$timestamp - Manual update to $newVersion (ver update manual)" | Add-Content -Path $historyPath
        Write-Host "Logged manual update to history" -ForegroundColor Cyan
    }

    # Обновление всех целей
    $filesUpdated = $false
    foreach ($target in $config.targets) {
        $filePath = Join-Path $rootPath $target.file
        if (-not (Test-Path $filePath)) {
            Write-Host "WARNING: $($target.file) not found" -ForegroundColor Yellow
            continue
        }

        $content = Get-Content -Path $filePath -Raw
        $replacement = $target.replacement -replace "{version}", $newVersion

        try {
            $newContent = $content -replace $target.pattern, $replacement
        }
        catch {
            Write-Host "ERROR: Invalid regex pattern for $($target.file) - $($_.Exception.Message)" -ForegroundColor Red
            continue
        }

        if ($content -ne $newContent) {
            Set-Content -Path $filePath -Value $newContent
            Write-Host "SUCCESS: $($target.file) updated" -ForegroundColor Green
            $filesUpdated = $true
        } else {
            Write-Host "INFO: $($target.file) already up-to-date" -ForegroundColor Cyan
        }
    }

    if ($filesUpdated) {
        Write-Host "SUCCESS: Version updated to $newVersion in all locations!" -ForegroundColor Green
    } else {
        if ($isManualCall) {
            Write-Host "INFO: All files already contain version $newVersion" -ForegroundColor Cyan
        }
    }
    exit 0
}
catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
