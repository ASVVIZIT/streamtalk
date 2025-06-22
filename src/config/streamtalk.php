<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Основное название мессенджера
    | Messenger Main Name
    |--------------------------------------------------------------------------
    |
    | Название, которое будет отображаться в заголовках и интерфейсе мессенджера.
    | The name that will be displayed in messenger titles and interface.
    |
    */
    'name' => env('STREAMTALK_NAME', 'StreamTalk Messenger'),

    /*
    |--------------------------------------------------------------------------
    | Диск хранилища
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | Файловый диск, используемый для хранения аватаров и вложений.
    | The filesystem disk used for storing avatars and attachments.
    |
    */
    'storage_disk_name' => env('STREAMTALK_STORAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Наименования таблиц
    | Table Names
    |--------------------------------------------------------------------------
    |
    | Названия таблиц, используемых мессенджером в базе данных.
    | Names of the database tables used by the messenger.
    |
    */
    'tables' => [
        'favorites' => 'st_favorites', // Таблица избранных контактов / Favorites table
        'messages' => 'st_messages',   // Таблица сообщений / Messages table
    ],

    /*
    |--------------------------------------------------------------------------
    | Наименования столбцов
    | Column Names
    |--------------------------------------------------------------------------
    |
    | Названия столбцов в таблице пользователей, используемых мессенджером.
    | Names of columns in the users table used by the messenger.
    |
    */
    'columns' => [
        'active_status' => 'st_active_status', // Статус активности / Active status
        'avatar' => 'st_avatar',               // Аватар пользователя / User avatar
        'dark_mode' => 'st_dark_mode',         // Режим темной темы / Dark mode
        'messenger_color' => 'st_messenger_color', // Цвет интерфейса / Messenger color
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки веб-маршрутов
    | Web Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Конфигурация маршрутов для веб-интерфейса мессенджера.
    | Configuration for messenger web interface routes.
    |
    */
    'routes' => [
        /*
        | Использовать кастомные маршруты
        | Use custom routes
        |
        | Если true, будут использоваться маршруты из routes/StreamTalk/web.php
        | If true, routes from routes/StreamTalk/web.php will be used
        */
        'custom' => env('STREAMTALK_CUSTOM_ROUTES', false),

        /*
        | Префикс URL для веб-маршрутов
        | URL prefix for web routes
        |
        | Пример: 'messenger' создаст маршруты вида /messenger/*
        | Example: 'messenger' will create routes like /messenger/*
        */
        'prefix' => env('STREAMTALK_ROUTES_PREFIX', 'streamtalk'),

        /*
        | Префикс имен маршрутов
        | Route names prefix
        |
        | Должен заканчиваться точкой. Пример: 'messenger.' создаст имена вида messenger.index
        | Should end with a dot. Example: 'messenger.' will create names like messenger.index
        */
        'as' => env('STREAMTALK_ROUTES_AS', 'streamtalk.'),

        /*
        | Middleware для веб-маршрутов
        | Middleware for web routes
        |
        | По умолчанию: веб-сессия и аутентификация
        | Default: web session and authentication
        */
        'middleware' => env('STREAMTALK_ROUTES_MIDDLEWARE', ['web','auth']),

        /*
        | Пространство имен контроллеров
        | Controllers namespace
        |
        | Пространство имен, где находятся контроллеры веб-интерфейса
        | Namespace where web controllers are located
        */
        'namespace' => env('STREAMTALK_ROUTES_NAMESPACE', 'App\Http\Controllers\StreamTalk'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки API-маршрутов
    | API Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Конфигурация маршрутов для API мессенджера.
    | Configuration for messenger API routes.
    |
    */
    'api_routes' => [
        /*
        | Префикс URL для API-маршрутов
        | URL prefix for API routes
        |
        | Пример: 'api/messenger' создаст маршруты вида /api/messenger/*
        | Example: 'api/messenger' will create routes like /api/messenger/*
        */
        'prefix' => env('STREAMTALK_API_ROUTES_PREFIX', 'streamtalk/api'),

        /*
        | Префикс имен API-маршрутов
        | Route names prefix for API
        |
        | Должен заканчиваться точкой. Пример: 'api.messenger.' создаст имена вида api.messenger.send
        | Should end with a dot. Example: 'api.messenger.' will create names like api.messenger.send
        */
        'as' => env('STREAMTALK_API_ROUTES_AS', 'api.streamtalk.'),

        /*
        | Middleware для API-маршрутов
        | Middleware for API routes
        |
        | По умолчанию: API и аутентификация Sanctum
        | Default: API and Sanctum authentication
        */
        'middleware' => env('STREAMTALK_API_ROUTES_MIDDLEWARE', ['api','auth:sanctum']),

        /*
        | Пространство имен API-контроллеров
        | Controllers namespace for API
        |
        | Пространство имен, где находятся API-контроллеры
        | Namespace where API controllers are located
        */
        'namespace' => env('STREAMTALK_API_ROUTES_NAMESPACE', 'App\Http\Controllers\StreamTalk\Api'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки Pusher
    | Pusher Configuration
    |--------------------------------------------------------------------------
    |
    | Конфигурация для интеграции с Pusher (WebSockets).
    | Configuration for Pusher integration (WebSockets).
    |
    */
    'pusher' => [
        'debug' => env('APP_DEBUG', false),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
            'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
            'port' => env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
            'encrypted' => true,
            'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки аватара пользователя
    | User Avatar Configuration
    |--------------------------------------------------------------------------
    |
    | Параметры для работы с аватарами пользователей.
    | Parameters for handling user avatars.
    |
    */
    'user_avatar' => [
        'folder' => 'users-avatar', // Папка для хранения аватарок / Folder for storing avatars
        'default' => 'avatar.png',  // Аватар по умолчанию / Default avatar
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки Gravatar
    | Gravatar Configuration
    |--------------------------------------------------------------------------
    |
    | Интеграция с сервисом Gravatar для генерации аватаров.
    | Integration with Gravatar service for avatar generation.
    |
    */
    'gravatar' => [
        'enabled' => true,         // Включить Gravatar / Enable Gravatar
        'image_size' => 200,       // Размер изображения / Image size
        'imageset' => 'identicon'  // Стиль изображения по умолчанию / Default image style
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки вложений
    | Attachments Configuration
    |--------------------------------------------------------------------------
    |
    | Параметры для работы с файловыми вложениями.
    | Parameters for handling file attachments.
    |
    */
    'attachments' => [
        'folder' => 'attachments', // Папка для хранения вложений / Folder for storing attachments

        /*
        | Имя маршрута для скачивания вложений
        | Route name for downloading attachments
        |
        | Используется без префикса группы маршрутов
        | Used without the route group prefix
        */
        'download_route_name' => 'attachments.download',

        'allowed_images' => ['png','jpg','jpeg','gif'], // Разрешенные изображения / Allowed images
        'allowed_files' => ['zip','rar','txt'],         // Разрешенные файлы / Allowed files

        /*
        | Максимальный размер загружаемого файла (МБ)
        | Maximum upload file size (MB)
        */
        'max_upload_size' => env('STREAMTALK_MAX_FILE_SIZE', 150),
    ],

    /*
    |--------------------------------------------------------------------------
    | Цветовая палитра
    | Color Palette
    |--------------------------------------------------------------------------
    |
    | Доступные цвета для выбора в интерфейсе мессенджера.
    | Available colors to choose in the messenger interface.
    |
    */
    'colors' => [
        '#2180f3', '#2196F3', '#00BCD4', '#3F51B5',
        '#673AB7', '#4CAF50', '#FFC107', '#FF9800',
        '#ff2522', '#9C27B0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Звуковые уведомления
    | Sound Notifications
    |--------------------------------------------------------------------------
    |
    | Настройки звуковых оповещений в мессенджере.
    | Sound notification settings in the messenger.
    |
    */
    'sounds' => [
        'enabled' => true, // Включить звуки / Enable sounds
        'public_path' => 'sounds/StreamTalk', // Путь к звуковым файлам / Path to sound files
        'new_message' => 'new-message-sound.mp3', // Звук нового сообщения / New message sound
    ]
];
