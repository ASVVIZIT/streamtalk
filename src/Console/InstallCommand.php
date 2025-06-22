<?php

namespace StreamTalk\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'StreamTalk:install {--force}';
    protected $description = 'Установка пакета StreamTalk';
    private $isV8;

    public function handle()
    {
        $this->isV8 = explode('.', app()->version())[0] >= 8;
        $this->info('Установка StreamTalk...');

        // Шаг 1: Конфигурация моделей
        $this->line('----------');
        $this->line('Конфигурация моделей и контроллеров...');

        // Обработка всех указанных файлов
        $this->modifyFile('/../Http/Controllers/StreamTalk/WebMessagesController.php', 'User');
        $this->modifyFile('/../Http/Controllers/StreamTalk/WebMessagesController.php', 'ChFavorite');
        $this->modifyFile('/../Http/Controllers/StreamTalk/WebMessagesController.php', 'ChMessage');

        $this->modifyFile('/../Http/Controllers/StreamTalk/Api/ApiMessagesController.php', 'User');
        $this->modifyFile('/../Http/Controllers/StreamTalk/Api/ApiMessagesController.php', 'ChFavorite');
        $this->modifyFile('/../Http/Controllers/StreamTalk/Api/ApiMessagesController.php', 'ChMessage');

        $this->modifyFile('/../StreamTalkMessenger.php', 'ChFavorite');
        $this->modifyFile('/../StreamTalkMessenger.php', 'ChMessage');

        $this->modifyFile('/../Models/StreamTalk/ChFavorite.php');
        $this->modifyFile('/../Models/StreamTalk/ChMessage.php');

        $this->info('[✓] Все файлы успешно сконфигурированы');

        // Шаг 2: Публикация ресурсов
        $assets = [
            'config' => config_path('streamtalk.php'),
            'views' => resource_path('views/vendor/StreamTalk'),
            'assets' => public_path('vendor/StreamTalk'),
            'migrations' => database_path('migrations'),
            'models' => app_path('Models/StreamTalk'),
            'controllers' => app_path('Http/Controllers/StreamTalk'),
            'routes' => base_path('routes/StreamTalk'),
        ];

        foreach ($assets as $target => $path) {
            $this->line('----------');
            $this->process($target, $path);
        }

        // Шаг 3: Создание симлинка хранилища
        $this->line('----------');
        $this->line('Создание симлинка хранилища...');
        Artisan::call('storage:link');
        $this->info('[✓] Симлинк создан');

        // Шаг 4: Выполнение миграций
        $this->line('----------');
        $this->line('Выполнение миграций...');
        Artisan::call('migrate');
        $this->info('[✓] Миграции выполнены');

        // Шаг 5: Очистка кэша
        $this->line('----------');
        $this->line('Очистка кэша...');
        Artisan::call('optimize:clear');
        $this->info('[✓] Кэш очищен');

        // Завершение установки
        $this->line('----------');
        $this->info('[✓] StreamTalk успешно установлен!');
    }

    /**
     * Модификация файлов с заменой путей к моделям
     *
     * @param string $relativePath Относительный путь к файлу
     * @param string|null $modelName Имя модели для замены (если указано)
     */
    private function modifyFile($relativePath, $modelName = null)
    {
        $fullPath = realpath(__DIR__ . $relativePath);

        if (!file_exists($fullPath)) {
            $this->error("Файл не найден: {$fullPath}");
            return;
        }

        $contents = File::get($fullPath);
        $newContents = $this->replaceModelPaths($contents, $modelName);

        File::put($fullPath, $newContents);
        $this->line("Обработан: " . basename($fullPath));
    }

    /**
     * Замена путей к моделям в содержимом файла
     *
     * @param string $contents Исходное содержимое файла
     * @param string|null $modelName Имя модели для замены
     * @return string Модифицированное содержимое
     */
    private function replaceModelPaths($contents, $modelName = null)
    {
        // Базовая замена для всех файлов
        $replacements = [
            'App\Models\ChMessage' => 'App\Models\StreamTalk\ChMessage',
            'App\Models\ChFavorite' => 'App\Models\StreamTalk\ChFavorite',
            'namespace App\Http\Controllers\vendor\StreamTalk' => 'namespace App\Http\Controllers\StreamTalk',
        ];

        // Дополнительные замены для конкретных моделей
        if ($modelName) {
            $oldModel = "App\Models\\{$modelName}";
            $newModel = $this->isV8
                ? "App\Models\StreamTalk\\{$modelName}"
                : "App\StreamTalk\\{$modelName}";

            $replacements[$oldModel] = $newModel;
        }

        // Применяем все замены
        foreach ($replacements as $old => $new) {
            $contents = str_replace($old, $new, $contents);
        }

        return $contents;
    }

    // Обработка публикации ресурсов
    private function process($target, $path)
    {
        $this->line("Публикация {$target}...");

        // Создание директории, если не существует
        if (!File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        if (!File::exists($path) || $this->option('force')) {
            $this->publish($target, true);
            $this->info('[✓] Опубликовано');
        } else {
            $this->line('[-] Пропущено (используйте --force для перезаписи)');
        }
    }

    // Вызов публикации
    private function publish($tag, $force = false)
    {
        $params = ['--tag' => 'StreamTalk-'.$tag];
        if ($force) $params['--force'] = true;
        $this->call('vendor:publish', $params);
    }
}
