<?php

namespace StreamTalk\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'StreamTalk:publish {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all of the StreamTalk assets';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if($this->option('force')){
            $this->call('vendor:publish', [
                '--tag' => 'StreamTalk-config',
                '--force' => true,
            ]);

            $this->call('vendor:publish', [
                '--tag' => 'StreamTalk-migrations',
                '--force' => true,
            ]);

            $this->call('vendor:publish', [
                '--tag' => 'StreamTalk-models',
                '--force' => true,
            ]);
        }

        $this->call('vendor:publish', [
            '--tag' => 'StreamTalk-views',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'StreamTalk-assets',
            '--force' => true,
        ]);
    }
}
