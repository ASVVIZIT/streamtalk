<?php

namespace StreamTalk;

use StreamTalk\Console\InstallCommand;
use StreamTalk\Console\PublishCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StreamTalkServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Регистрация фасада StreamTalkMessenger
        app()->bind('StreamTalkMessenger', function () {
            return new \StreamTalk\StreamTalkMessenger;
        });
    }

    public function boot()
    {
        // Загрузка представлений
        $this->loadViewsFrom(__DIR__ . '/views', 'StreamTalk');
        $this->loadRoutes();

        // Регистрация консольных команд
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                PublishCommand::class,
            ]);
            $this->setPublishes();
        }
    }

    // Настройка публикуемых ресурсов
    protected function setPublishes()
    {
        $userAvatarFolder = config('streamtalk.user_avatar.folder', 'users-avatar');
        $separator = '_';

        // Конфигурация
        $this->publishes([
            __DIR__ . '/config/streamtalk.php' => config_path('streamtalk.php')
        ], 'StreamTalk-config');

        // Миграции (только объединенная)
        $this->publishes([
            __DIR__ . '/database/migrations/2022_01_10_99999_create_streamtalk_tables.php' =>
                database_path('migrations/' . date('Y_m_d') . $separator . date('His') . $separator . 'create_streamtalk_tables.php'),
        ], 'StreamTalk-migrations');

        // Модели
        $this->publishes([
            __DIR__ . '/Models' => app_path('Models/StreamTalk')
        ], 'StreamTalk-models');

        // Контроллеры
        $this->publishes([
            __DIR__ . '/Http/Controllers' => app_path('Http/Controllers/StreamTalk')
        ], 'StreamTalk-controllers');

        // Представления
        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/StreamTalk')
        ], 'StreamTalk-views');

        // Ресурсы (CSS/JS/Изображения/Звуки)
        $this->publishes([
            __DIR__ . '/assets/css' => public_path('css/StreamTalk'),
            __DIR__ . '/assets/js' => public_path('js/StreamTalk'),
            __DIR__ . '/assets/imgs' => storage_path('app/public/' . $userAvatarFolder),
            __DIR__ . '/assets/sounds' => public_path('sounds/StreamTalk'),
        ], 'StreamTalk-assets');

        // Маршруты
        $this->publishes([
            __DIR__ . '/routes' => base_path('routes/StreamTalk')
        ], 'StreamTalk-routes');
    }

    // Загрузка маршрутов
    protected function loadRoutes()
    {
        if (config('streamtalk.routes.custom')) {
            // Загрузка кастомных маршрутов
            Route::group($this->routesConfigurations(), function () {
                $this->loadRoutesFrom(base_path('routes/StreamTalk/web.php'));
            });
            Route::group($this->apiRoutesConfigurations(), function () {
                $this->loadRoutesFrom(base_path('routes/StreamTalk/api.php'));
            });
        } else {
            // Загрузка стандартных маршрутов
            Route::group($this->routesConfigurations(), function () {
                $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
            });
            Route::group($this->apiRoutesConfigurations(), function () {
                $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
            });
        }
    }

    // Конфигурация веб-маршрутов
    private function routesConfigurations()
    {
        return [
            'prefix' => config('streamtalk.routes.prefix'),
            'namespace' =>  config('streamtalk.routes.namespace'),
            'middleware' => config('streamtalk.routes.middleware'),
            'as' => 'streamtalk.' // Добавлен префикс для имен маршрутов
        ];
    }

    // Конфигурация API маршрутов
    private function apiRoutesConfigurations()
    {
        return [
            'prefix' => config('streamtalk.api_routes.prefix'),
            'namespace' =>  config('streamtalk.api_routes.namespace'),
            'middleware' => array_merge(
                config('streamtalk.api_routes.middleware'),
                ['auth:sanctum']
            ),
            'as' => 'api.streamtalk.' // Префикс для API маршрутов
        ];
    }
}
