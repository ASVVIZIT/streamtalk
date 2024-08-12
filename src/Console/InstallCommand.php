<?php

namespace StreamTalk\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streamtalk:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install StreamTalk package';

    /**
     * Check Laravel version.
     *
     * @var bool
     */
    private $isV8;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->isV8 = explode('.',app()->version())[0] >= 8;

        $this->info('Installing StreamTalk...');

        $this->line('----------');
        $this->line('Configurations...');
        $this->modifyModelsPath('/../Http/Controllers/MessagesController.php','User');
        $this->modifyModelsPath('/../Http/Controllers/MessagesController.php','ChFavorite');
        $this->modifyModelsPath('/../Http/Controllers/MessagesController.php','ChMessage');
        $this->modifyModelsPath('/../Http/Controllers/Api/MessagesController.php','User');
        $this->modifyModelsPath('/../Http/Controllers/Api/MessagesController.php','ChFavorite');
        $this->modifyModelsPath('/../Http/Controllers/Api/MessagesController.php','ChMessage');
        $this->modifyModelsPath('/../StreamTalkMessenger.php','ChFavorite');
        $this->modifyModelsPath('/../StreamTalkMessenger.php','ChMessage');
        $this->modifyModelsPath('/../Models/ChFavorite.php');
        $this->modifyModelsPath('/../Models/ChMessage.php');
        $this->info('[✓] done');

        $assetsToBePublished = [
            'config' => config_path('streamtalk.php'),
            'views' => resource_path('views/vendor/StreamTalk'),
            'assets' => public_path('css/streamtalk'),
            'models' => app_path(($this->isV8 ? 'Models/' : '').'ChMessage.php'),
            'migrations' => database_path('migrations/2024_09_22_192348_create_messages_table.php'),
            'routes' => base_path('routes/streamtalk'),
        ];

        foreach ($assetsToBePublished as $target => $path) {
            $this->line('----------');
            $this->process($target, $path);
        }

        $this->line('----------');
        $this->line('Creating storage symlink...');
        Artisan::call('storage:link');
        $this->info('[✓] Storage linked.');

        $this->line('----------');
        $this->info('[✓] StreamTalk installed successfully');
    }

    /**
     * Modify models imports/namespace path according to Laravel version.
     *
     * @param string $targetFilePath
     * @param string $model
     * @return void
     */
    private function modifyModelsPath($targetFilePath, $model = null){
        $path = realpath(__DIR__.$targetFilePath);
        $contents = File::get($path);
        $model = !empty($model) ? '\\'.$model : ';';
        $contents = str_replace(
            (!$this->isV8 ? 'App\Models' : 'App').$model,
            ($this->isV8 ? 'App\Models' : 'App').$model,
            $contents
        );
        File::put($path, $contents);
    }

    /**
     * Check, publish, or overwrite the assets.
     *
     * @param string $target
     * @param string $path
     * @return void
     */
    private function process($target, $path)
    {
        $this->line('Publishing '.$target.'...');
        if (!File::exists($path)) {
            $this->publish($target);
            $this->info('[✓] '.$target.' published.');
            return;
        }
        if ($this->shouldOverwrite($target)) {
            $this->line('Overwriting '.$target.'...');
            $this->publish($target,true);
            $this->info('[✓] '.$target.' published.');
            return;
        }
        $this->line('[-] Ignored, The existing '.$target.' was not overwritten');
    }

    /**
     * Ask to overwrite.
     *
     * @param string $target
     * @return void
     */
    private function shouldOverwrite($target)
    {
        return $this->confirm(
            $target.' already exists. Do you want to overwrite it?',
            false
        );
    }

    /**
     * Call the publish command.
     *
     * @param string $tag
     * @param bool $forcePublish
     * @return void
     */
    private function publish($tag, $forcePublish = false)
    {
        $this->call('vendor:publish', [
            '--tag' => 'streamtalk-'.$tag,
            '--force' => $forcePublish,
        ]);
    }
}
