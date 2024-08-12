<?php

namespace streamtalk;

use sreamtalk\Console\InstallCommand;
use sreamtalk\Console\PublishCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StreamTalkServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind('StreamTalkMessenger', function () {
            return new \streamtalk\StreamTalkMessenger;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load Views and Routes
        $this->loadViewsFrom(__DIR__ . '/views', 'streamtalk');
        $this->loadRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                PublishCommand::class,
            ]);
            $this->setPublishes();
        }
    }

    /**
     * Publishing the files that the user may override.
     *
     * @return void
     */
    protected function setPublishes()
    {
        // Load user's avatar folder from package's config
        $userAvatarFolder = json_decode(json_encode(include(__DIR__.'/config/streamtalk.php')))->user_avatar->folder;

        // Config
        $this->publishes([
            __DIR__ . '/config/streamtalk.php' => config_path('streamtalk.php')
        ], 'streamtalk-config');

        // Migrations

        $separator = '_';
        $this->publishes([
            __DIR__ . '/database/migrations/2022_01_10_99999_add_active_status_to_users.php' => database_path('migrations/' . date('Y_m_d') . $separator . date('His') . $separator . 'add_active_status_to_users.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_add_avatar_to_users.php' => database_path('migrations/' . date('Y_m_d') . $separator . date('His') . $separator . 'add_avatar_to_users.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_add_dark_mode_to_users.php' => database_path('migrations/' . date('Y_m_d') . $separator . date('His') . $separator . 'add_dark_mode_to_users.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_add_messenger_color_to_users.php' => database_path('migrations/' . date('Y_m_d') . $separator . date('His') . $separator . 'add_messenger_color_to_users.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_create_streamtalk_favorites_table.php' => database_path('migrations/' . date('Y_m_d') . $separator . date('His') . $separator . 'create_streamtalk_favorites_table.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_create_streamtalk_messages_table.php' => database_path('migrations/' . date('Y_m_d') . $separator . date('His') . $separator . 'create_streamtalk_messages_table.php'),
        ], 'streamtalk-migrations');

        // Models
        $isV8 = explode('.', app()->version())[0] >= 8;
        $this->publishes([
            __DIR__ . '/Models' => app_path($isV8 ? 'Models' : '')
        ], 'streamtalk-models');

        // Controllers
        $this->publishes([
            __DIR__ . '/Http/Controllers' => app_path('Http/Controllers/vendor/StreamTalk')
        ], 'streamtalk-controllers');

        // Views
        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/StreamTalk')
        ], 'streamtalk-views');

        // Assets
        $this->publishes([
            // CSS
            __DIR__ . '/assets/css' => public_path('css/streamtalk'),
            // JavaScript
            __DIR__ . '/assets/js' => public_path('js/streamtalk'),
            // Images
            __DIR__ . '/assets/imgs' => storage_path('app/public/' . $userAvatarFolder),
             // CSS
             __DIR__ . '/assets/sounds' => public_path('sounds/streamtalk'),
        ], 'streamtalk-assets');

        // Routes (API and Web)
        $this->publishes([
            __DIR__ . '/routes' => base_path('routes/streamtalk')
        ], 'streamtalk-routes');
    }

    /**
     * Group the routes and set up configurations to load them.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        if (config('streamtalk.routes.custom')) {
            Route::group($this->routesConfigurations(), function () {
                $this->loadRoutesFrom(base_path('routes/streamtalk/web.php'));
            });
            Route::group($this->apiRoutesConfigurations(), function () {
                $this->loadRoutesFrom(base_path('routes/streamtalk/api.php'));
            });
        } else {
            Route::group($this->routesConfigurations(), function () {
                $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
            });
            Route::group($this->apiRoutesConfigurations(), function () {
                $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
            });
        }
    }

    /**
     * Routes configurations.
     *
     * @return array
     */
    private function routesConfigurations()
    {
        return [
            'prefix' => config('streamtalk.routes.prefix'),
            'namespace' =>  config('streamtalk.routes.namespace'),
            'middleware' => config('streamtalk.routes.middleware'),
        ];
    }
    /**
     * API routes configurations.
     *
     * @return array
     */
    private function apiRoutesConfigurations()
    {
        return [
            'prefix' => config('streamtalk.api_routes.prefix'),
            'namespace' =>  config('streamtalk.api_routes.namespace'),
            'middleware' => config('streamtalk.api_routes.middleware'),
        ];
    }
}
