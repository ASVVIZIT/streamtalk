<?php

namespace StreamTalk\Console;

use Illuminate\Console\Command;

class VersionCommand extends Command
{
    protected $signature = 'streamtalk:version {version}';
    protected $description = 'Update StreamTalk version in all locations';

    public function handle()
    {
        $newVersion = $this->argument('version');

        // Обновляем version.php
        file_put_contents(__DIR__.'/../version.php', "<?php\nreturn '$newVersion';\n");

        // Обновляем composer.json
        $composer = json_decode(file_get_contents(__DIR__.'/../../composer.json'), true);
        $composer['version'] = $newVersion;
        file_put_contents(__DIR__.'/../../composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Обновляем .env.example
        $envExample = file_get_contents(__DIR__.'/../../.env.example');
        $envExample = preg_replace(
            '/STREAMTALK_VERSION=.*/',
            "STREAMTALK_VERSION=$newVersion",
            $envExample
        );
        file_put_contents(__DIR__.'/../../.env.example', $envExample);

        $this->info("✅ Version updated to $newVersion in all locations!");
    }
}
