<?php

namespace StreamTalk;

use StreamTalk\Console\InstallCommand;
use StreamTalk\Console\PublishCommand;
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
            return new \StreamTalk\StreamTalkMessenger;
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
        $this->loadViewsFrom(__DIR__ . '/views', 'StreamTalk');
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
        $userAvatarFolder = json_decode(json_encode(include(__DIR__.'/config/StreamTalk.php')))->user_avatar->folder;

        // Config
        $this->publishes([
            __DIR__ . '/config/StreamTalk.php' => config_path('StreamTalk.php')
        ], 'StreamTalk-config');

        // Migrations
        $this->publishes([
            __DIR__ . '/database/migrations/2022_01_10_99999_add_active_status_to_users.php' => database_path('migrations/' . date('Y_m_d') . '_999999_add_active_status_to_users.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_add_avatar_to_users.php' => database_path('migrations/' . date('Y_m_d') . '_999999_add_avatar_to_users.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_add_dark_mode_to_users.php' => database_path('migrations/' . date('Y_m_d') . '_999999_add_dark_mode_to_users.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_add_messenger_color_to_users.php' => database_path('migrations/' . date('Y_m_d') . '_999999_add_messenger_color_to_users.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_create_StreamTalk_favorites_table.php' => database_path('migrations/' . date('Y_m_d') . '_999999_create_StreamTalk_favorites_table.php'),
            __DIR__ . '/database/migrations/2022_01_10_99999_create_StreamTalk_messages_table.php' => database_path('migrations/' . date('Y_m_d') . '_999999_create_StreamTalk_messages_table.php'),
        ], 'StreamTalk-migrations');

        // Models
        $isV8 = explode('.', app()->version())[0] >= 8;
        $this->publishes([
            __DIR__ . '/Models' => app_path($isV8 ? 'Models' : '')
        ], 'StreamTalk-models');

        // Controllers
        $this->publishes([
            __DIR__ . '/Http/Controllers' => app_path('Http/Controllers/vendor/StreamTalk')
        ], 'StreamTalk-controllers');

        // Views
        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/StreamTalk')
        ], 'StreamTalk-views');

        // Assets
        $this->publishes([
            // CSS
            __DIR__ . '/assets/css' => public_path('css/StreamTalk'),
            // JavaScript
            __DIR__ . '/assets/js' => public_path('js/StreamTalk'),
            // Images
            __DIR__ . '/assets/imgs' => storage_path('app/public/' . $userAvatarFolder),
             // CSS
             __DIR__ . '/assets/sounds' => public_path('sounds/StreamTalk'),
        ], 'StreamTalk-assets');

        // Routes (API and Web)
        $this->publishes([
            __DIR__ . '/routes' => base_path('routes/StreamTalk')
        ], 'StreamTalk-routes');
    }

    /**
     * Group the routes and set up configurations to load them.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        if (config('StreamTalk.routes.custom')) {
            Route::group($this->routesConfigurations(), function () {
                $this->loadRoutesFrom(base_path('routes/StreamTalk/web.php'));
            });
            Route::group($this->apiRoutesConfigurations(), function () {
                $this->loadRoutesFrom(base_path('routes/StreamTalk/api.php'));
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
            'prefix' => config('StreamTalk.routes.prefix'),
            'namespace' =>  config('StreamTalk.routes.namespace'),
            'middleware' => config('StreamTalk.routes.middleware'),
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
            'prefix' => config('StreamTalk.api_routes.prefix'),
            'namespace' =>  config('StreamTalk.api_routes.namespace'),
            'middleware' => config('StreamTalk.api_routes.middleware'),
        ];
    }
}
