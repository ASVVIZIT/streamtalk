<?php

namespace StreamTalk\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

/**
 * Команда установки пакета StreamTalk
 * StreamTalk package installation command
 */
class InstallCommand extends Command
{
    protected $signature = 'streamtalk:install {--force}';
    protected $description = 'Install StreamTalk package with all necessary resources and file modifications';

    public function handle()
    {
        $steps = 8; // Общее количество шагов
        // Получаем версию из единого источника
        $version = include __DIR__.'/../version.php';

        $this->line("🚀 Starting StreamTalk v{$version} installation...");
        $this->line('----------------------------------------');

        // Шаг 1: Публикация конфигурации
        $this->line("[1/{$steps}] 📝 Publishing configuration...");
        $this->call('vendor:publish', [
            '--provider' => 'StreamTalk\StreamTalkServiceProvider',
            '--tag' => 'streamtalk-config',
            '--force' => $this->option('force')
        ]);

        // Шаг 2: Публикация ресурсов
        $this->line("[2/{$steps}] 🖼️ Publishing assets...");
        $this->call('vendor:publish', [
            '--provider' => 'StreamTalk\StreamTalkServiceProvider',
            '--tag' => 'streamtalk-assets',
            '--force' => $this->option('force')
        ]);

        // Шаг 3: Публикация представлений
        $this->line("[3/{$steps}] 👀 Publishing views...");
        $this->call('vendor:publish', [
            '--provider' => 'StreamTalk\StreamTalkServiceProvider',
            '--tag' => 'streamtalk-views',
            '--force' => $this->option('force')
        ]);

        // Шаг 4: Модификация файлов
        $this->line("[4/{$steps}] 🔧 Modifying files...");
        $this->modifyFiles();

        // Шаг 5: Публикация миграций
        $this->line("[5/{$steps}] 🗃️ Publishing migrations...");
        $this->call('vendor:publish', [
            '--provider' => 'StreamTalk\StreamTalkServiceProvider',
            '--tag' => 'streamtalk-migrations',
            '--force' => $this->option('force')
        ]);

        // Шаг 6: Выполнение миграций
        $this->line("[6/{$steps}] ⚙️ Running migrations...");
        Artisan::call('migrate');

        // Шаг 7: Создание симлинка хранилища
        $this->line("[7/{$steps}] 🔗 Creating storage link...");
        Artisan::call('storage:link');

        // Шаг 8: Оптимизация приложения
        $this->line("[8/{$steps}] ⚡ Optimizing application...");
        Artisan::call('config:cache');
        Artisan::call('route:cache');

        $this->newLine();
        $this->line("✅ Finish StreamTalk v{$version} installed successfully!");
        $this->line('----------------------------------------');
        $this->line('Next steps:');
        $this->line('1. Configure Pusher credentials in .env file');
        $this->line('2. Run: npm install && npm run dev');
        $this->line('3. Include chat component: @include("streamtalk::layouts.app")');
        $this->line('4. Configure your filesystems.php for storage');
        $this->line('----------------------------------------');
    }

    /**
     * Модификация файлов контроллеров и моделей
     * Modify controller and model files
     */
    protected function modifyFiles()
    {
        // Модификация веб-контроллеров
        $this->modifyFile('/../Http/Controllers/WebMessagesController.php', 'User');
        $this->modifyFile('/../Http/Controllers/WebMessagesController.php', 'Favorite');
        $this->modifyFile('/../Http/Controllers/WebMessagesController.php', 'Message');

        // Модификация API-контроллеров
        $this->modifyFile('/../Http/Controllers/Api/ApiMessagesController.php', 'User');
        $this->modifyFile('/../Http/Controllers/Api/ApiMessagesController.php', 'Favorite');
        $this->modifyFile('/../Http/Controllers/Api/ApiMessagesController.php', 'Message');

        // Модификация основного класса мессенджера
        $this->modifyFile('/../StreamTalkMessenger.php', 'Favorite');
        $this->modifyFile('/../StreamTalkMessenger.php', 'Message');

        // Модификация моделей
        $this->modifyFile('/../Models/Favorite.php');
        $this->modifyFile('/../Models/Message.php');
    }

    /**
     * Модификация конкретного файла
     * Modify specific file
     *
     * @param string $relativePath Относительный путь к файлу
     * @param string|null $modelName Имя модели для замены
     */
    protected function modifyFile($relativePath, $modelName = null)
    {
        $fullPath = realpath(__DIR__ . $relativePath);

        if (!file_exists($fullPath)) {
            $this->warn("⚠️ File not found: {$fullPath}");
            return;
        }

        $this->line("Processing: " . basename($fullPath));

        $contents = File::get($fullPath);
        $newContents = $this->replaceModelPaths($contents, $modelName);

        if ($contents !== $newContents) {
            File::put($fullPath, $newContents);
            $this->info("✓ Modified: " . basename($fullPath));
        } else {
            $this->line("- No changes needed");
        }
    }

    /**
     * Замена путей моделей на новые пространства имен
     * Replace model paths with new namespaces
     *
     * @param string $contents Содержимое файла
     * @param string|null $modelName Имя модели
     * @return string Обновленное содержимое
     */
    protected function replaceModelPaths($contents, $modelName = null)
    {
        $replacements = [
            'App\Models\ChMessage' => 'StreamTalk\Models\Message',
            'App\Models\ChFavorite' => 'StreamTalk\Models\Favorite',
            'App\Models\Message' => 'StreamTalk\Models\Message',
            'App\Models\Favorite' => 'StreamTalk\Models\Favorite',
            'ChMessage' => 'Message',
            'ChFavorite' => 'Favorite',
            'namespace App\Http\Controllers\vendor\StreamTalk' => 'namespace StreamTalk\Http\Controllers',
        ];

        if ($modelName) {
            $oldModel = "App\Models\\{$modelName}";
            $newModel = "StreamTalk\Models\\{$modelName}";
            $replacements[$oldModel] = $newModel;

            // Добавляем замену для коротких имен
            $replacements[$modelName] = $modelName;
        }

        foreach ($replacements as $old => $new) {
            $contents = str_replace($old, $new, $contents);
        }

        return $contents;
    }
}
