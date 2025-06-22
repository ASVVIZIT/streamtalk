<?php

namespace StreamTalk;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use StreamTalk\Console\InstallCommand;
use StreamTalk\Console\PublishCommand;

/**
 * Сервис-провайдер пакета StreamTalk
 * StreamTalk package service provider
 */
class StreamTalkServiceProvider extends ServiceProvider
{
    /**
     * Регистрация сервисов
     * Register services
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/streamtalk.php', 'streamtalk'
        );

        $this->app->singleton('StreamTalkMessenger', function ($app) {
            return new \StreamTalk\StreamTalkMessenger;
        });
    }

    /**
     * Загрузка сервисов
     * Bootstrap services
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'streamtalk');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                PublishCommand::class,
            ]);

            $this->configurePublishing();
        }
    }

    /**
     * Настройка публикации ресурсов
     * Configure publishable resources
     */
    protected function configurePublishing()
    {
        // Конфигурация
        // Configuration
        $this->publishes([
            __DIR__ . '/../config/streamtalk.php' => config_path('streamtalk.php')
        ], 'streamtalk-config');

        // Миграции
        // Migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations')
        ], 'streamtalk-migrations');

        // Представления
        // Views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/streamtalk')
        ], 'streamtalk-views');

        // Ресурсы (JS, CSS, изображения, звуки)
        // Assets (JS, CSS, images, sounds)
        $this->publishes([
            __DIR__ . '/../resources/js' => public_path('vendor/streamtalk/js'),
            __DIR__ . '/../resources/css' => public_path('vendor/streamtalk/css'),
            __DIR__ . '/../resources/images' => public_path('vendor/streamtalk/images'),
            __DIR__ . '/../resources/sounds' => public_path('vendor/streamtalk/sounds'),
        ], 'streamtalk-assets');

        // Модели (опционально)
        // Models (optional)
        $this->publishes([
            __DIR__ . '/../Models' => app_path('Models/StreamTalk')
        ], 'streamtalk-models');

        // Контроллеры (опционально)
        // Controllers (optional)
        $this->publishes([
            __DIR__ . '/../Http/Controllers' => app_path('Http/Controllers/StreamTalk')
        ], 'streamtalk-controllers');
    }

    /**
     * Загрузка маршрутов
     * Load routes
     */
    protected function loadRoutes()
    {
        if (config('streamtalk.routes.enabled', true)) {
            // Веб-маршруты
            // Web routes
            Route::prefix(config('streamtalk.routes.prefix', 'streamtalk'))
                ->middleware(config('streamtalk.routes.middleware', ['web', 'auth']))
                ->namespace('StreamTalk\Http\Controllers')
                ->as('streamtalk.')
                ->group(function () {
                    $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
                });

            // API-маршруты
            // API routes
            Route::prefix(config('streamtalk.api_routes.prefix', 'api/streamtalk'))
                ->middleware(config('streamtalk.api_routes.middleware', ['api', 'auth:sanctum']))
                ->namespace('StreamTalk\Http\Controllers\Api')
                ->as('api.streamtalk.')
                ->group(function () {
                    $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
                });
        }
    }
}
