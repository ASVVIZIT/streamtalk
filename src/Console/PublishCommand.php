<?php

namespace StreamTalk\Console;

use Illuminate\Console\Command;

/**
 * Команда для публикации ресурсов StreamTalk
 * Command for publishing StreamTalk resources
 */
class PublishCommand extends Command
{
    protected $signature = 'streamtalk:publish 
                            {--force : Перезаписать существующие файлы}
                            {--assets : Публикация только ассетов}
                            {--config : Публикация только конфигурации}
                            {--views : Публикация только представлений}';

    protected $description = 'Публикация ресурсов StreamTalk с детализированным выводом';

    public function handle()
    {
        $force = $this->option('force');

        if ($this->option('assets')) {
            $this->publishAssets($force);
            return;
        }

        if ($this->option('config')) {
            $this->publishConfig($force);
            return;
        }

        if ($this->option('views')) {
            $this->publishViews($force);
            return;
        }

        $this->publishAll($force);
    }

    /**
     * Публикация всех ресурсов
     * Publish all resources
     *
     * @param bool $force Принудительная перезапись
     */
    protected function publishAll($force)
    {
        $this->info('Publishing all StreamTalk resources...');

        $this->publishConfig($force);
        $this->publishViews($force);
        $this->publishAssets($force);

        $this->info('✅ All resources published successfully!');
    }

    /**
     * Публикация конфигурации
     * Publish configuration
     *
     * @param bool $force Принудительная перезапись
     */
    protected function publishConfig($force)
    {
        $this->line('Publishing configuration...');
        $this->call('vendor:publish', [
            '--provider' => 'StreamTalk\StreamTalkServiceProvider',
            '--tag' => 'streamtalk-config',
            '--force' => $force
        ]);
        $this->info('✅ Configuration published!');
    }

    /**
     * Публикация представлений
     * Publish views
     *
     * @param bool $force Принудительная перезапись
     */
    protected function publishViews($force)
    {
        $this->line('Publishing views...');
        $this->call('vendor:publish', [
            '--provider' => 'StreamTalk\StreamTalkServiceProvider',
            '--tag' => 'streamtalk-views',
            '--force' => $force
        ]);
        $this->info('✅ Views published!');
    }

    /**
     * Публикация ассетов
     * Publish assets
     *
     * @param bool $force Принудительная перезапись
     */
    protected function publishAssets($force)
    {
        $this->line('Publishing assets...');
        $this->call('vendor:publish', [
            '--provider' => 'StreamTalk\StreamTalkServiceProvider',
            '--tag' => 'streamtalk-assets',
            '--force' => $force
        ]);
        $this->info('✅ Assets published!');
    }
}
